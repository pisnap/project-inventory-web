<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Borrowing;
use Filament\Tables;

class CreateBorrowing extends CreateRecord
{
    // public $name = 'Pisnap';

    // // protected static string $view = 'filament.pages.create-borrowing';

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
            'borrow_action' => 'Borrow',                      // Aksi
            'borrow_date' => now(),                             // Tanggal
        ]);

        \App\Models\Borrowing::where('id', $this->record->id)
            ->update(['user_id' => auth()->id()]);

        \App\Models\Stock_item::where('code', $this->record->code_item)
            ->update(['status' => 'Borrow']);
    }

    // protected function getFormActions(): array
    // {
    //     return [];
    // }

    // protected function getHeaderWidgets(): array
    // {
    //     return [];
    // }

    // public function formColumns(): int
    // {
    //     return 3;
    // }

    // public function getTable(): Tables\Table
    // {
    //     return Tables\Table::make()
    //         ->query(Borrowing::query()->orderBy('updated_at', 'desc'))
    //         ->columns([
    //             TextColumn::make('borrower_name')->label('Name'),
    //             TextColumn::make('code_item')->label('Code'),
    //             TextColumn::make('borrowed_item')->label('Borrowed Item'),
    //             TextColumn::make('amount')->label('Amount'),
    //             TextColumn::make('borrow_date')->label('Borrow Date'),
    //             TextColumn::make('status')
    //                 ->badge()
    //                 ->colors([
    //                     'success' => 'Return',
    //                     'danger' => 'Borrow',
    //                 ]),
    //             ImageColumn::make('image')
    //                 ->label('Image')
    //                 ->size(100)
    //                 ->getStateUsing(function ($record) {
    //                     return $record->stock_item ? $record->stock_item->image : null;
    //                 }),
    //         ])
    //         ->actions([
    //             ViewAction::make(),
    //             EditAction::make(),
    //             DeleteAction::make(),
    //         ])
    //         ->bulkActions([]);
    // }

    // protected function getContent(): string
    // {
    //     return view('filament::pages.create-borrowing', [
    //         'form' => $this->form,
    //         'table' => $this->getTable(),
    //     ])->render();
    // }
}
