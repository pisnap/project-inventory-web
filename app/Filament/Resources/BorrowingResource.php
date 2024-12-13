<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowingResource\Pages;
use App\Filament\Resources\BorrowingResource\RelationManagers;
use App\Models\Borrowing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ButtonAction;
use Filament\Forms\Components\Select;
use PhpParser\Node\Stmt\Label;
use App\Models\Stock_item;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Facades\Filament;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;

class BorrowingResource extends Resource
{
    protected static ?string $model = Borrowing::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationLabel = 'Borrow';
    protected static ?string $label = 'Borrowing Item';
    protected static ?string $slug = 'borrow-item';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('borrower_name')
                    ->required()
                    ->columnSpanFull()
                    ->label('Name')
                    ->default(auth()->user()->name),
                TextInput::make('code_item')
                    ->autofocus()
                    ->required()
                    ->reactive()
                    ->label('Code')
                    ->rules(['exists:stock_items,code'])
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                        $latestBorrowing = \App\Models\Borrowing::where('code_item', $state)->first();

                        if ($latestBorrowing && $latestBorrowing->status !== 'Return') {
                            $set('code_item', null); // Reset nilai code_item
                            $set('amount', null);
                            $set('borrowed_item', null);
                            \Filament\Notifications\Notification::make()
                                ->title('Item Unavailable')
                                ->body('This item is currently borrowed and cannot be borrowed again.')
                                ->danger()
                                ->send();
                        } else {
                            // Ambil nama item dari tabel stock_items
                            $stockItem = \App\Models\Stock_item::where('code', $state)->first();

                            if ($stockItem) {
                                $set('borrowed_item', $stockItem->items);
                                $set('amount', $stockItem->stock); // Atur nilai borrowed_item berdasarkan nama
                            } else {
                                $set('borrowed_item', null);
                                $set('amount', null); // Jika code_item tidak ditemukan
                            }
                        }
                    }),
                TextInput::make('borrowed_item')
                    ->validationMessages([
                        'required' => 'You cannot select an item that is already borrowed, and this field cannot be empty.',
                    ])
                    ->label('Item Name')
                    ->rules(['exists:stock_items,items']),
                TextInput::make('amount')
                    ->numeric()
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                        $codeItem = $get('code_item');
                        $stockItem = Stock_item::where('code', $codeItem)->first();

                        if ($stockItem && $state > $stockItem->stock) {
                            \Filament\Notifications\Notification::make()
                                ->title('Invalid Amount')
                                ->body('Amount cannot exceed available stock (' . $stockItem->stock . ').')
                                ->danger()
                                ->send();

                            $set('amount', null); // Reset field amount jika invalid
                        }
                    }),
                TextInput::make('borrow_date')
                    ->label('Borrow Date')
                    ->default(now()->format('d-m-Y'))
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Format: dd-mm-yyyy'),
                TextInput::make('status')
                    ->default('Borrow')
                    ->columnSpanFull()
                    ->disabled(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->query(function () {
                $user = auth()->user();

                return $user->role === 'Admin'
                    ? Borrowing::query()->orderBy('updated_at', 'desc') // Admin dapat melihat semua data
                    : Borrowing::query()->where('user_id', $user->id)->orderBy('updated_at', 'desc'); // User hanya melihat data mereka
            })
            ->columns([
                TextColumn::make('borrower_name')
                    ->label('Name'),
                TextColumn::make('code_item')
                    ->label('Code'),
                TextColumn::make('borrowed_item')
                    ->label('Borrowed Item'),
                TextColumn::make('amount'),
                TextColumn::make('borrow_date')
                    ->label('Borrow Date'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'Return',
                        'danger' => 'Borrow',
                    ]),
                ImageColumn::make('image')
                    ->size(150)
                    ->getStateUsing(function ($record) {
                        // Mengambil gambar dari relasi stock_item
                        return $record->stock_item ? $record->stock_item->image : null;
                    }),
            ])
            ->filters([
                SelectFilter::make('Borrower_name')
                    ->label('Name')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Borrowing::pluck('borrower_name', 'borrower_name')->unique()->toArray();
                    }),
                SelectFilter::make('code_item')
                    ->label('Code')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Borrowing::pluck('code_item', 'code_item')->unique()->toArray();
                    }),
                SelectFilter::make('borrowed_item')
                    ->label('Borrowed Item')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Borrowing::pluck('borrowed_item', 'borrowed_item')->unique()->toArray();
                    }),
                SelectFilter::make('borrow_date')
                    ->label('Borrow Date')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Borrowing::pluck('borrow_date', 'borrow_date')->unique()->toArray();
                    }),
                SelectFilter::make('status')
                    ->label('Status')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Borrowing::pluck('status', 'status')->unique()->toArray();
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrowings::route('/'),
            'create' => Pages\CreateBorrowing::route('/create'),
            'edit' => Pages\EditBorrowing::route('/{record}/edit'),
        ];
    }
}
