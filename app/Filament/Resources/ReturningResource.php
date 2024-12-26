<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturningResource\Pages;
use App\Filament\Resources\ReturningResource\RelationManagers;
use App\Models\Returning;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\Action;
use Filament\Facades\Filament;

use PhpParser\Node\Stmt\Return_;

class ReturningResource extends Resource
{
    protected static ?string $model = Returning::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Return';
    protected static ?string $label = 'Returning Item';
    protected static ?string $slug = 'return-item';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('returner_name')
                    ->required()
                    ->columnSpanFull()
                    ->label('Name')
                    ->default(auth()->user()->name),
                TextInput::make('code_item')
                    ->required()
                    ->label('Code')
                    ->reactive()
                    ->autofocus()
                    ->rules(['exists:borrowings,code_item'])
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                        $state = strtoupper($state);
                        $set('code_item', $state);
                        // Cari data borrowing berdasarkan code_item
                        $stockItem = \App\Models\Stock_item::where('code', $state)->first();
                        if (!$stockItem) {
                            return; // Keluar dari fungsi jika tidak ditemukan
                        }
                        elseif ($stockItem->status === 'Available') {
                            $set('code_item', null); // Reset nilai code_item
                            $set('amount', null);
                            $set('returned_item', null);
                            \Filament\Notifications\Notification::make()
                                ->title('Non-Returnable Item')
                                ->body('This item is not found in the borrowing records and cannot be returned.')
                                ->danger()
                                ->send();
                        } else {
                            // Ambil data item dari tabel Stock_item jika ada
                            if ($stockItem) {
                                $set('returned_item', $stockItem->items); // Mengambil nama item
                                $set('amount', $stockItem->stock); // Mengambil jumlah stok item
                            } else {
                                $set('returned_item', null);
                                $set('amount', null); // Jika code_item tidak ditemukan di Stock_item
                            }
                        }
                    }),
                TextInput::make('returned_item')
                    ->label('Item Name')
                    ->rules(['exists:borrowings,borrowed_item']),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Dapatkan code_item dari form
                        $codeItem = $get('code_item');

                        // Cari data borrowings berdasarkan code_item
                        $borrowing = \App\Models\Borrowing::where('code_item', $codeItem)->first();

                        if ($borrowing) {
                            // Jika amount lebih besar dari jumlah di borrowings, tampilkan pesan error
                            $borrowedAmount = $borrowing->amount;
                            if ($state > $borrowedAmount) {
                                $set('amount', null); // Reset nilai amount
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Invalid Amount')
                                    ->body("the amount must be equal to the amount borrowed.")
                                    ->send();
                            }
                        }
                    }),
                TextInput::make('return_date')
                    ->required()
                    ->columnSpanFull()
                    ->label('Return Date')
                    ->default(now()->format('d-m-Y'))
                    ->required()
                    ->helperText('Format: dd-mm-yyyy'),
                TextInput::make('status')
                    ->columnSpanFull()
                    ->default('Return')
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
                    ? Returning::query()->orderBy('updated_at', 'desc') // Admin dapat melihat semua data
                    : Returning::query()->where('user_id', $user->id)->orderBy('updated_at', 'desc'); // User hanya melihat data mereka
            })
            ->columns([
                TextColumn::make('returner_name')
                    ->label('Name'),
                TextColumn::make('code_item')
                    ->label('Code'),
                TextColumn::make('returned_item')
                    ->label('Returned Item'),
                TextColumn::make('amount'),
                TextColumn::make('return_date')
                    ->label('Return Date'),
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
                SelectFilter::make('returner_name')
                    ->label('Name')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Returning::pluck('returner_name', 'returner_name')->unique()->toArray();
                    }),
                SelectFilter::make('code_item')
                    ->label('Code')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Returning::pluck('code_item', 'code_item')->unique()->toArray();
                    }),
                SelectFilter::make('returned_item')
                    ->label('Returned Item')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Returning::pluck('returned_item', 'returned_item')->unique()->toArray();
                    }),
                SelectFilter::make('return_date')
                    ->label('Return Date')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Returning::pluck('return_date', 'return_date')->unique()->toArray();
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
            'index' => Pages\ListReturnings::route('/'),
            'create' => Pages\CreateReturning::route('/create'),
            'edit' => Pages\EditReturning::route('/{record}/edit'),
        ];
    }
}
