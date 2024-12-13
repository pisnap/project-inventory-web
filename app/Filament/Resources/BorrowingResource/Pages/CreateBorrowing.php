<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\Action;
use Filament\Notifications\Notification;

class CreateBorrowing extends CreateRecord
{
    protected static string $resource = BorrowingResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Borrow')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Item Borrowed')
            ->body('The item has been successfully borrowed and recorded in the system.');
    }

    protected function afterCreate(): void
    {
        // Tambahkan ke tabel histories
        \App\Models\History::create([
            'user_name' => $this->record->borrower_name, // Nama peminjam
            'item_name' => $this->record->borrowed_item,     // Nama item
            'code_item' => $this->record->code_item,     // Kode item
            'action' => 'Borrow',                      // Aksi
            'date' => now(),                             // Tanggal
        ]);

        \App\Models\Borrowing::where('id', $this->record->id)
            ->update(['user_id' => auth()->id()]);

        \App\Models\Stock_item::where('code', $this->record->code_item)
            ->update(['status' => 'Borrow']);
    }
}
