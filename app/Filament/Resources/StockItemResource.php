<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use App\Models\Stock_item;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Exports\StockItemExporter;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\StockItemResource\Pages;

class StockItemResource extends Resource
{
    protected static ?string $model = Stock_item::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'All Item';
    protected static ?string $label = 'Item';
    protected static ?string $slug = 'all-item';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.resources.users.pages.view-user';

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
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $state = strtoupper($state); // Ubah menjadi huruf kapital
                        $set('code', $state); // Tetapkan nilai huruf kapital kembali ke input
                    }),

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
                TextColumn::make('items')
                    ->label('Item'),
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
                        ->beforeReplicaSaved(function (Stock_item $replica): void {
                            // Ambil nilai asli dari kolom 'code'
                            $originalCode = $replica->code;

                            // Variabel untuk menyimpan code baru
                            $newCode = $originalCode . '-copy';

                            // Cek apakah code baru sudah ada dalam database
                            $counter = 1;
                            while ($replica->where('code', $newCode)->exists()) {
                                $newCode = $originalCode . '-copy-' . $counter;
                                $counter++;
                            }

                            // Set code baru ke model replica
                            $replica->code = $newCode;
                        }),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(StockItemExporter::class)
                        ->label('Export Data')
                        ->icon('heroicon-o-document-arrow-up'),
                    Tables\Actions\DeleteBulkAction::make(),
                ])->label('Action'),
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
