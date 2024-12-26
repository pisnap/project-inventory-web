<?php

namespace App\Filament\Widgets;

use App\Models\Stock_item;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TotalItems extends BaseWidget
{
    protected static ?int $sort = -2;

    protected function getCards(): array
    {
        // Total items
        $totalItems = Stock_item::count();

        // Items in good condition
        $goodItems = Stock_item::where('condition', 'good')->count();

        // Items in broken condition
        $brokenItems = Stock_item::where('condition', 'broken')->count();

        // Borrowed items
        $borrowedItems = Stock_item::whereHas('borrowings', function ($query) {
            $query->where('status', 'borrow');
        })->count();

        return [
            Card::make('Total Items', $totalItems)
                ->description('All items in inventory')
                ->color('primary'),

            Card::make('Good Items', $goodItems)
                ->description('Items in good condition')
                ->color('success'),

            Card::make('Broken Items', $brokenItems)
                ->description('Items in broken condition')
                ->color('danger'),

            Card::make('Borrowed Items', $borrowedItems)
                ->description('Items currently borrowed')
                ->color('warning'),
        ];
    }
}
