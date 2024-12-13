<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoryResource\Pages;
use App\Filament\Resources\HistoryResource\RelationManagers;
use App\Models\History;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;

class HistoryResource extends Resource
{
    protected static ?string $model = History::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'History';
    protected static ?string $slug = 'history';
    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->query(
                History::query()->orderBy('date', 'desc')
            )
            ->columns([
                TextColumn::make('user_name')
                    ->label('Name'),
                TextColumn::make('item_name')
                    ->label('Item'),
                TextColumn::make('code_item')
                    ->label('Code'),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->colors([
                        'success' => 'Return',
                        'danger' => 'Borrow',
                    ]),
                TextColumn::make('date')
                    ->label('Date'),
            ])
            ->filters([
                SelectFilter::make('user_name')
                    ->label('Name')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\History::pluck('user_name', 'user_name')->unique()->toArray();
                    }),
                    SelectFilter::make('item_name')
                    ->label('Item')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\History::pluck('item_name', 'item_name')->unique()->toArray();
                    }),
                    SelectFilter::make('code_item')
                    ->label('Code')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\History::pluck('code_item', 'code_item')->unique()->toArray();
                    }),
                    SelectFilter::make('action')
                    ->label('Action')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\History::pluck('action', 'action')->unique()->toArray();
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistories::route('/'),
            'create' => Pages\CreateHistory::route('/create'),
            'edit' => Pages\EditHistory::route('/{record}/edit'),
        ];
    }
}
