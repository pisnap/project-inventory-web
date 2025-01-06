<?php

namespace App\Filament\Resources\StockItemResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Stock_item;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Exports\StockItemExporter;
use App\Filament\Imports\StockItemImporter;
use App\Filament\Resources\StockItemResource;

class ListStockItems extends ListRecords
{
    protected static string $resource = StockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Update Category')
                ->icon('heroicon-o-tag')
                ->label('Update Category')
                ->modalHeading('Update Category by Item Name')
                ->action(function (array $data): void {
                    \App\Models\Stock_item::where('items', $data['item'])
                        ->update(['category' => $data['category']]);

                    // Add a notification after successful update
                    Notification::make()
                        ->title('Category Updated')
                        ->success()
                        ->body("The category for item '{$data['item']}' has been successfully updated.")
                        ->send();
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

                    Select::make('category')
                        ->searchable()
                        ->label('New Category')
                        ->options(\App\Models\Category::query()
                            ->pluck('category', 'category')) // Fetch categories from the Category table
                        ->required()
                        ->placeholder('Select a category')
                        ->createOptionForm([
                            TextInput::make('category')
                                ->label('New Category')
                                ->required()
                                ->placeholder('Enter new category name'),
                        ])
                        ->createOptionUsing(function (array $data) {
                            // Simpan kategori baru ke tabel `categories`
                            $newCategory = \App\Models\Category::create(['category' => $data['category']]);

                            // Kembalikan ID dari kategori yang baru dibuat
                            return $newCategory->category;
                        }),
                ]),

            Actions\Action::make('Update Images')
                ->icon('heroicon-o-photo')
                ->label('Update Images')
                ->modalHeading('Update Image by Item Name')
                ->action(function (array $data): void {
                    \App\Models\Stock_item::where('items', $data['item'])
                        ->update(['image' => $data['image']]);

                    // Add a notification after successful update
                    Notification::make()
                        ->title('Image Updated')
                        ->success()
                        ->body("The image for item '{$data['item']}' has been successfully updated.")
                        ->send();
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
