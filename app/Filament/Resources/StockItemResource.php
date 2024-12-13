<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockItemResource\Pages;
use App\Filament\Resources\StockItemResource\RelationManagers;
use App\Models\Stock_item;
use App\Models\Borrowing;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;


class StockItemResource extends Resource
{
    protected static ?string $model = Stock_item::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'All Item';
    protected static ?string $label = 'Item';
    protected static ?string $slug = 'all-item';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->image()
                    ->columnSpanFull(),
                Select::make('category')
                    ->label('Category')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->options(Category::pluck('category', 'category'))
                    ->createOptionForm([
                        Forms\Components\TextInput::make('category')
                            ->label('New Category')
                            ->required()
                            ->placeholder('Enter new category name'),
                    ])
                    ->createOptionUsing(function (array $data) {
                        // Simpan kategori baru ke tabel `categories`
                        $newCategory = Category::create(['category' => $data['category']]);

                        // Kembalikan ID dari kategori yang baru dibuat
                        return $newCategory->category;
                    }),
                TextInput::make('code')
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('items')
                    ->required(),
                TextInput::make('unit')
                    ->required(),
                TextInput::make('stock')
                    ->required()
                    ->default('1'),
                Select::make('condition')
                    ->native(false)
                    ->options([
                        'Good' => 'Good',
                        'Broken' => 'Broken',
                    ])
                    ->required(),
                TextInput::make('location')
                    ->required(),
                Select::make('status')
                    ->native(false)
                    ->options([
                        'Borrow' => 'Borrow',
                        'Available' => 'Available',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $query = Stock_item::query()
            ->select('stock_items.*')
            ->orderBy('stock_items.category', 'asc');

        return $table
            ->query($query)
            ->recordUrl(null)
            ->columns([
                ImageColumn::make('image')
                    ->size(150),
                TextColumn::make('category'),
                TextColumn::make('code'),
                TextColumn::make('items'),
                TextColumn::make('unit'),
                TextColumn::make('stock'),
                TextColumn::make('condition')
                    ->badge()
                    ->colors([
                        'success' => 'Good',
                        'danger' => 'Broken',
                    ]),
                TextColumn::make('location'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'Available',
                        'warning' => 'Borrow',
                    ])
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Stock_item::pluck('category', 'category')->unique()->toArray();
                    }),
                SelectFilter::make('code')
                    ->label('Code')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Stock_item::pluck('code', 'code')->unique()->toArray();
                    }),
                SelectFilter::make('items')
                    ->label('Item Name')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Stock_item::pluck('items', 'items')->unique()->toArray();
                    }),
                SelectFilter::make('condition')
                    ->label('Condition')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Stock_item::pluck('condition', 'condition')->unique()->toArray();
                    }),
                SelectFilter::make('location')
                    ->label('Location')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Stock_item::pluck('location', 'location')->unique()->toArray();
                    }),
                SelectFilter::make('status')
                    ->label('Status')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Stock_item::pluck('status', 'status')->unique()->toArray();
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
                    ReplicateAction::make()
                        ->excludeAttributes(['code', 'status']),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
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
            'index' => Pages\ListStockItems::route('/'),
            'create' => Pages\CreateStockItem::route('/create'),
            'edit' => Pages\EditStockItem::route('/{record}/edit'),
        ];
    }
}
