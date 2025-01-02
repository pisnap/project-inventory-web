<?php

namespace App\Filament\Resources\BorrowingResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Auth;

class TableBorrowingWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getBorrowingQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('borrower_name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('code_item')
                    ->label('Code'),
                Tables\Columns\TextColumn::make('borrowed_item')
                    ->label('Borrowed Name'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('borrow_date')
                    ->label('Borrow Date'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'Borrow',
                    ]),
            ]);
    }

    protected function getBorrowingQuery()
    {
        if (auth()->user()->role === 'Admin') {
            // Admin dapat melihat semua data
            return Borrowing::where('status', 'Borrow')
                ->orderBy('updated_at', 'desc');
        }

        // User hanya dapat melihat data miliknya
        return Borrowing::where('status', 'Borrow')
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
    }
}
