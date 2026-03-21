<?php

namespace App\Filament\Resources\GradeResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Subject Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_core')
                    ->label('Core Subject')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_mandatory')
                    ->label('Mandatory')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->pivot->is_mandatory ?? false),

                Tables\Columns\TextColumn::make('credit_hours')
                    ->label('Credit Hours')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
