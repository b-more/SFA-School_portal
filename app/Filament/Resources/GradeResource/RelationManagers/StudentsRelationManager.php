<?php

namespace App\Filament\Resources\GradeResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_id_number')
                    ->label('Student ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('classSection.name')
                    ->label('Class Section')
                    ->default('N/A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gender')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('DOB')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('parentGuardian.name')
                    ->label('Parent/Guardian')
                    ->default('N/A')
                    ->searchable(),

                Tables\Columns\TextColumn::make('parentGuardian.phone')
                    ->label('Contact')
                    ->default('N/A'),

                Tables\Columns\BadgeColumn::make('enrollment_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'info' => 'graduated',
                        'danger' => fn ($state) => !in_array($state, ['active', 'inactive', 'graduated']),
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),

                Tables\Filters\SelectFilter::make('enrollment_status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                        'transferred' => 'Transferred',
                        'expelled' => 'Expelled',
                    ]),

                Tables\Filters\SelectFilter::make('class_section_id')
                    ->label('Class Section')
                    ->relationship('classSection', 'name'),
            ])
            ->defaultSort('name', 'asc')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByRaw('LOWER(TRIM(name)) ASC'))
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.students.edit', $record)),
            ])
            ->bulkActions([]);
    }
}
