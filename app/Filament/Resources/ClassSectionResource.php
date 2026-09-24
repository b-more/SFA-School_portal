<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\ClassSectionResource\Pages;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassSectionResource extends Resource
{
    protected static ?string $model = ClassSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Academic Management';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return ! in_array(auth()->user()?->role_id, [RoleConstants::LIBRARIAN, RoleConstants::TEACHER, RoleConstants::PARENT, RoleConstants::STUDENT, RoleConstants::DRIVER, RoleConstants::CLINICIAN, RoleConstants::NURSE]) ?? false;
    }

    /**
     * Does this grade belong to the Secondary school section?
     */
    public static function gradeIsSecondary($gradeId): bool
    {
        if (! $gradeId) {
            return false;
        }

        return Grade::query()
            ->where('id', $gradeId)
            ->whereHas('schoolSection', fn ($q) => $q->where('code', 'SEC'))
            ->exists();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('academic_year_id')
                ->label('Academic Year')
                ->options(AcademicYear::pluck('name', 'id'))
                ->required(),

            Forms\Components\Select::make('grade_id')
                ->label('Grade')
                ->options(Grade::pluck('name', 'id'))
                ->required()
                ->live(),

            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            // Every class section MUST have exactly one class teacher (homeroom / attendance
            // teacher) — both Primary and Secondary. In Primary/ECE they teach all subjects;
            // in Secondary they teach only their own subject and other subjects are taught
            // by visiting subject teachers.
            Forms\Components\Select::make('class_teacher_id')
                ->label('Class Teacher (Homeroom)')
                ->helperText('One teacher per class: marks attendance, manages this class, writes report-card comments. Secondary classes still get one — they teach only their specialty subject; other subjects are taught by subject teachers.')
                ->options(fn () => Teacher::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(fn (Forms\Get $get) => self::gradeIsSecondary($get('grade_id')))
                ->nullable(fn (Forms\Get $get) => ! self::gradeIsSecondary($get('grade_id'))),

            Forms\Components\TextInput::make('capacity')
                ->numeric()
                ->default(40),

            Forms\Components\Toggle::make('is_active')
                ->default(true),

            Forms\Components\Textarea::make('description')
                ->maxLength(1000)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),

            Tables\Columns\TextColumn::make('grade.name')
                ->label('Grade')
                ->sortable(),

            Tables\Columns\TextColumn::make('academicYear.name')
                ->label('Academic Year')
                ->sortable(),

            // Direct relationship to Teacher model
            Tables\Columns\TextColumn::make('classTeacher.name')
                ->label('Class Teacher')
                ->searchable()
                ->placeholder('Not Assigned'),

            Tables\Columns\TextColumn::make('capacity')
                ->numeric()
                ->sortable(),

            Tables\Columns\IconColumn::make('is_active')
                ->boolean(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('grade_id')
                ->label('Grade')
                ->options(Grade::pluck('name', 'id')),

            Tables\Filters\SelectFilter::make('academic_year_id')
                ->label('Academic Year')
                ->options(AcademicYear::pluck('name', 'id')),

            Tables\Filters\TernaryFilter::make('is_active')
                ->label('Active Status'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            // If you need relationship managers, define them here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassSections::route('/'),
            'create' => Pages\CreateClassSection::route('/create'),
            'edit' => Pages\EditClassSection::route('/{record}/edit'),
        ];
    }
}
