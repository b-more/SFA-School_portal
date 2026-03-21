<?php

namespace App\Filament\Resources\GradeResource\RelationManagers;

use App\Models\AcademicYear;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassSectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'classSections';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Section Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('classTeacher.name')
                    ->label('Class Teacher')
                    ->default('N/A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Students')
                    ->getStateUsing(fn ($record) => $record->students()->where('enrollment_status', 'active')->count())
                    ->sortable(false),

                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(fn () => AcademicYear::orderByDesc('is_active')->orderByDesc('start_date')->pluck('name', 'id'))
                    ->default(fn () => AcademicYear::where('is_active', true)->first()?->id),

                Tables\Filters\Filter::make('is_active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active only')
                    ->toggle()
                    ->default(true),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
