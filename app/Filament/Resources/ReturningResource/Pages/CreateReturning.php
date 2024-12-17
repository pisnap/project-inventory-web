<?php

namespace App\Filament\Resources\ReturningResource\Pages;

use App\Filament\Resources\ReturningResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\Action;
use Filament\Notifications\Notification;

class CreateReturning extends CreateRecord
{
    protected static string $resource = ReturningResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Return')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Item Returned')
            ->body('The item has been successfully returned to the inventory.');
    }

    protected function afterCreate(): void
    {
        \App\Models\History::where('code_item', $this->record->code_item)
            ->update([
                'return_action' => 'Return',
                'return_date' => now(),
            ]);

        // Update status di tabel borrowings
        \App\Models\Borrowing::where('code_item', $this->record->code_item)
            ->update(['status' => 'Return']);

        \App\Models\Returning::where('code_item', $this->record->code_item)
            ->update(['user_id' => auth()->id()]);

        \App\Models\Stock_item::where('code', $this->record->code_item)
            ->update(['status' => 'Available']);
    }
}
