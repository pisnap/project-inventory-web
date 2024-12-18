<?php

namespace App\Filament\Exports;

use App\Models\Stock_item;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StockItemExporter extends Exporter
{
    protected static ?string $model = Stock_item::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('category'),
            ExportColumn::make('code'),
            ExportColumn::make('items')
                ->label('Item'),
            ExportColumn::make('unit'),
            ExportColumn::make('stock'),
            ExportColumn::make('condition'),
            ExportColumn::make('location'),
            ExportColumn::make('status'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your stock item export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
