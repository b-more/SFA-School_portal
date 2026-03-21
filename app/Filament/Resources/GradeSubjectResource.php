<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeSubjectResource\Pages;
use App\Filament\Resources\GradeSubjectResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\GradeSubject;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentSubjectEnrollment;
use App\Models\Subject;
use App\Constants\RoleConstants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class GradeSubjectResource extends Resource
{
    protected static ?string $model = GradeSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?string $navigationLabel = 'Grade Subjects';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role_id, [RoleConstants::ADMIN, RoleConstants::SCHOOL_SECRETARY]) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Grade Subject Assignment')
                    ->description('Assign subjects to grades and specify if they are mandatory')
                    ->schema([
                        Forms\Components\Select::make('grade_id')
                            ->label('Grade')
                            ->relationship('grade', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('subject_id')
                            ->label('Subject')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Toggle::make('is_mandatory')
                            ->label('Mandatory Subject')
                            ->helperText('Is this subject mandatory for this grade?')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grade.name')
                    ->label('Grade')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.code')
                    ->label('Subject Code')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_mandatory')
                    ->label('Mandatory')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('enrollment_status')
                    ->label('Enrolled')
                    ->state(function ($record) {
                        if ($record->is_mandatory) {
                            return 'All';
                        }
                        $academicYear = AcademicYear::where('is_active', true)->first();
                        if (!$academicYear) {
                            return '0';
                        }
                        return (string) StudentSubjectEnrollment::where('subject_id', $record->subject_id)
                            ->where('grade_id', $record->grade_id)
                            ->where('academic_year_id', $academicYear->id)
                            ->count();
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === 'All' => 'success',
                        $state === '0' => 'gray',
                        default => 'info',
                    })
                    ->searchable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('teachers_display')
                    ->label('Teachers Assigned')
                    ->getStateUsing(function ($record) {
                        $teachers = $record->teachers;

                        if ($teachers->isEmpty()) {
                            return '—';
                        }

                        return $teachers->map(function ($teacher) {
                            return $teacher->name . ' (' . $teacher->class_section_name . ')';
                        })->join(', ');
                    })
                    ->wrap()
                    ->searchable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('grade.name', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('grade_id')
                    ->label('Grade')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_mandatory')
                    ->label('Mandatory Subjects')
                    ->placeholder('All subjects')
                    ->trueLabel('Mandatory only')
                    ->falseLabel('Optional only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manageEnrollment')
                    ->label('Manage Enrollment')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn ($record) => !$record->is_mandatory && auth()->user()?->role_id === RoleConstants::ADMIN)
                    ->modalHeading(fn ($record) => "Manage Enrollment: {$record->subject->name} ({$record->grade->name})")
                    ->modalDescription('Select students to enroll in this optional subject.')
                    ->modalSubmitActionLabel('Save Enrollment')
                    ->form(function ($record) {
                        $academicYear = AcademicYear::where('is_active', true)->first();
                        if (!$academicYear) {
                            return [];
                        }

                        $students = Student::where('grade_id', $record->grade_id)
                            ->where('enrollment_status', 'active')
                            ->orderBy('name')
                            ->get();

                        $options = $students->mapWithKeys(function ($student) {
                            return [$student->id => $student->name];
                        })->toArray();

                        $descriptions = $students->mapWithKeys(function ($student) {
                            $classSection = $student->classSection?->name ?? 'No class';
                            return [$student->id => "{$student->student_id_number} — {$classSection}"];
                        })->toArray();

                        $currentlyEnrolled = StudentSubjectEnrollment::where('subject_id', $record->subject_id)
                            ->where('grade_id', $record->grade_id)
                            ->where('academic_year_id', $academicYear->id)
                            ->pluck('student_id')
                            ->toArray();

                        return [
                            Forms\Components\CheckboxList::make('student_ids')
                                ->label('Students')
                                ->options($options)
                                ->descriptions($descriptions)
                                ->searchable()
                                ->bulkToggleable()
                                ->columns(2)
                                ->default($currentlyEnrolled),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $academicYear = AcademicYear::where('is_active', true)->first();
                        if (!$academicYear) {
                            Notification::make()
                                ->title('No active academic year found')
                                ->danger()
                                ->send();
                            return;
                        }

                        $selectedIds = $data['student_ids'] ?? [];

                        // Remove enrollments for students no longer selected
                        StudentSubjectEnrollment::where('subject_id', $record->subject_id)
                            ->where('grade_id', $record->grade_id)
                            ->where('academic_year_id', $academicYear->id)
                            ->whereNotIn('student_id', $selectedIds)
                            ->delete();

                        // Add new enrollments
                        foreach ($selectedIds as $studentId) {
                            StudentSubjectEnrollment::firstOrCreate(
                                [
                                    'student_id' => $studentId,
                                    'subject_id' => $record->subject_id,
                                    'academic_year_id' => $academicYear->id,
                                ],
                                [
                                    'grade_id' => $record->grade_id,
                                    'enrolled_by' => Auth::id(),
                                ]
                            );
                        }

                        Notification::make()
                            ->title('Enrollment updated')
                            ->body(count($selectedIds) . ' student(s) enrolled.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('setMandatory')
                        ->label('Mark as Mandatory')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_mandatory' => true]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('setOptional')
                        ->label('Mark as Optional')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_mandatory' => false]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListGradeSubjects::route('/'),
            'create' => Pages\CreateGradeSubject::route('/create'),
            'edit' => Pages\EditGradeSubject::route('/{record}/edit'),
        ];
    }
}
