<?php

namespace App\Filament\Resources\StockItemResource\Pages;

use App\Filament\Resources\StockItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Exports\StockItemExporter;
use App\Filament\Imports\StockItemImporter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Support\Colors\Color;
use App\Models\Stock_item;
use App\Models\User;

class ListStockItems extends ListRecords
{
    protected static string $resource = StockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Update Images')
                ->icon('heroicon-o-photo')
                ->label('Update Images')
                ->modalHeading('Update Image by Item Name')
                ->action(function (array $data): void {
                    \App\Models\Stock_item::where('items', $data['item'])
                        ->update(['image' => $data['image']]);
                })
                ->form([
                    Select::make('item')
                        ->label('Item Name')
                        ->options(\App\Models\Stock_item::query()
                            ->select('items')
                            ->distinct()
                            ->pluck('items', 'items'))
                        ->required()
                        ->searchable()
                        ->placeholder('Select an item'),

                    FileUpload::make('image')
                        ->label('Image')
                        ->image()
                        ->required(),
                ]),
            Actions\ImportAction::make()
                ->color('primary')
                ->label('Import Data')
                ->modalHeading('Import Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->importer(StockItemImporter::class)
                ->hidden(fn() => auth()->user()->role !== 'Admin'),
            Actions\ExportAction::make()
                ->color('primary')
                ->label('Export Data')
                ->modalHeading('Export Data')
                ->icon('heroicon-o-arrow-up-tray')
                ->exporter(StockItemExporter::class)
                ->hidden(fn() => auth()->user()->role !== 'Admin'),
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Add New Item'),
        ];
    }
}
