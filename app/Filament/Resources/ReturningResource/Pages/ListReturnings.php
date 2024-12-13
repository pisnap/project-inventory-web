<?php

namespace App\Filament\Resources\ReturningResource\Pages;

use App\Filament\Resources\ReturningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReturnings extends ListRecords
{
    protected static string $resource = ReturningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Returning Items'),
        ];
    }
}
