<?php

namespace App\Filament\Widgets;

use App\Models\Stock_item;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class CategorySummaryWidget extends BaseWidget
{
    protected int | string | array $columnSpan = '50%';

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->query(
                Stock_item::query()
                    ->selectRaw('
                        MIN(stock_items.id) as id,
                        stock_items.category,
                        COUNT(*) as total_items,
                        SUM(CASE WHEN stock_items.condition = "good" THEN 1 ELSE 0 END) as good_items,
                        SUM(CASE WHEN stock_items.condition = "broken" THEN 1 ELSE 0 END) as broken_items,
                        SUM(CASE WHEN (
                            SELECT status
                            FROM borrowings
                            WHERE borrowings.code_item = stock_items.code
                            ORDER BY updated_at DESC
                            LIMIT 1
                        ) = "borrow" THEN 1 ELSE 0 END) as borrowed_items
                    ')
                    ->groupBy('stock_items.category')
                    ->orderBy('stock_items.category')
            )
            ->columns([
                TextColumn::make('category')
                    ->searchable()
                    ->label('Category')
                    ->badge()
                    ->url(fn($record) => route('filament.admin.resources.all-item.index', [
                        'tableFilters[category][value]' => $record->category,
                    ])),
                TextColumn::make('total_items')
                    ->label('Total')
                    ->badge()
                    ->color('primary')
                    ->url(fn($record) => route('filament.admin.resources.all-item.index', [
                        'tableFilters[category][value]' => $record->category,
                    ])),
                TextColumn::make('good_items')
                    ->label('Good')
                    ->badge()
                    ->color('success')
                    ->url(fn($record) => route('filament.admin.resources.all-item.index', [
                        'tableFilters[category][value]' => $record->category,
                        'tableFilters[condition][value]' => 'Good',
                    ])),
                TextColumn::make('broken_items')
                    ->label('Broken')
                    ->badge()
                    ->color('danger')
                    ->url(fn($record) => route('filament.admin.resources.all-item.index', [
                        'tableFilters[category][value]' => $record->category,
                        'tableFilters[condition][value]' => 'Broken',
                    ])),
                TextColumn::make('borrowed_items')
                    ->label('Borrow')
                    ->badge()
                    ->color('warning')
                    ->url(fn($record) => route('filament.admin.resources.all-item.index', [
                        'tableFilters[category][value]' => $record->category,
                        'tableFilters[status][value]' => 'Borrow',
                    ])),
            ]);
    }
}
