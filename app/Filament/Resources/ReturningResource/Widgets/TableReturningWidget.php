<?php

namespace App\Filament\Resources\ReturningResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Returning;
use Illuminate\Support\Facades\Auth;

class TableReturningWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getBorrowingQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('returner_name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('code_item')
                    ->label('Code'),
                Tables\Columns\TextColumn::make('returned_item')
                    ->label('Borrowed Name'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Borrow Date'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'Return',
                    ]),
            ]);
    }

    protected function getBorrowingQuery()
    {
        if (auth()->user()->role === 'Admin') {
            // Admin dapat melihat semua data
            return Returning::where('status', 'Return')
                ->orderBy('updated_at', 'desc');
        }

        // User hanya dapat melihat data miliknya
        return Returning::where('status', 'Return')
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
    }
}
