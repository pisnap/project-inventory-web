<?php

namespace App\Filament\Resources\StockItemResource\Pages;

use App\Filament\Resources\StockItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Exports\StockItemExporter;
use App\Models\Stock_item;
use App\Models\User;

class ListStockItems extends ListRecords
{
    protected static string $resource = StockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
            ->label('Export Data')
            ->icon('heroicon-o-arrow-down-tray')
            ->exporter(StockItemExporter::class),
            // ->action(function () {
            //     $this->authorize('export', Stock_item::class);
            // })
            // ->hidden(fn (User $user) => ! $user->can('export', Stock_item::class)),
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus')
            ->label('Add New Item'),
        ];
    }

}
