<?php

namespace App\Filament\Imports;

use App\Models\Stock_item;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class StockItemImporter extends Importer
{
    protected static ?string $model = Stock_item::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('category'),
            ImportColumn::make('code'),
            ImportColumn::make('items')
                ->label('Item'),
            ImportColumn::make('unit'),
            ImportColumn::make('stock'),
            ImportColumn::make('condition'),
            ImportColumn::make('location'),
            ImportColumn::make('status'),
        ];
    }

    public function resolveRecord(): ?Stock_item
    {
        return Stock_item::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'code' => $this->data['code'],
        ]);

        return new Stock_item();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your stock item import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
