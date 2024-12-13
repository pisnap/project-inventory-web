<?php

namespace App\Filament\Resources\ReturningResource\Pages;

use App\Filament\Resources\ReturningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReturning extends EditRecord
{
    protected static string $resource = ReturningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
