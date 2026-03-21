<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\NoticeResource\Pages;
use App\Models\ClassSection;
use App\Models\Grade;
use App\Models\Notice;
use App\Models\SchoolSection;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationLabel = 'Notices';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(Auth::user()?->role_id, [
            RoleConstants::ADMIN,
            ...RoleConstants::teaching(),
            ...RoleConstants::management(),
            RoleConstants::SCHOOL_SECRETARY,
        ]);
    }

    public static function canCreate(): bool
    {
        return in_array(Auth::user()?->role_id, [
            RoleConstants::ADMIN,
            ...RoleConstants::teaching(),
            ...RoleConstants::management(),
            RoleConstants::SCHOOL_SECRETARY,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Notice Details')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('body')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('priority')
                        ->options([
                            'normal' => 'Normal',
                            'important' => 'Important',
                            'urgent' => 'Urgent',
                        ])
                        ->default('normal')
                        ->required()
                        ->native(false),

                    Forms\Components\FileUpload::make('attachment')
                        ->directory('notices')
                        ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(10240)
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable(),
                ])->columns(2),

            Forms\Components\Section::make('Target Audience')
                ->description('Select who should see this notice')
                ->schema([
                    Forms\Components\Select::make('target_type')
                        ->label('Send To')
                        ->options([
                            'school' => 'Whole School',
                            'section' => 'Section (Primary / Secondary)',
                            'grade' => 'Specific Grade',
                            'class' => 'Specific Class',
                            'student' => 'Individual Student',
                        ])
                        ->default('school')
                        ->required()
                        ->live()
                        ->native(false),

                    Forms\Components\Select::make('target_section_id')
                        ->label('Section')
                        ->options(SchoolSection::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->visible(fn (Forms\Get $get) => $get('target_type') === 'section')
                        ->required(fn (Forms\Get $get) => $get('target_type') === 'section')
                        ->native(false),

                    Forms\Components\Select::make('target_grade_id')
                        ->label('Grade')
                        ->options(Grade::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->visible(fn (Forms\Get $get) => in_array($get('target_type'), ['grade', 'class']))
                        ->required(fn (Forms\Get $get) => in_array($get('target_type'), ['grade', 'class']))
                        ->live()
                        ->native(false),

                    Forms\Components\Select::make('target_class_id')
                        ->label('Class Section')
                        ->options(function (Forms\Get $get) {
                            $gradeId = $get('target_grade_id');
                            if (!$gradeId) return [];
                            return ClassSection::where('grade_id', $gradeId)
                                ->where('is_active', true)
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->visible(fn (Forms\Get $get) => $get('target_type') === 'class')
                        ->required(fn (Forms\Get $get) => $get('target_type') === 'class')
                        ->native(false),

                    Forms\Components\Select::make('target_student_id')
                        ->label('Student')
                        ->options(Student::where('enrollment_status', 'active')->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->visible(fn (Forms\Get $get) => $get('target_type') === 'student')
                        ->required(fn (Forms\Get $get) => $get('target_type') === 'student')
                        ->native(false),
                ])->columns(2),

            Forms\Components\Section::make('Publishing')
                ->schema([
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Publish Date')
                        ->default(now())
                        ->helperText('Leave as now to publish immediately'),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Expiry Date')
                        ->helperText('Leave empty for no expiry'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\Hidden::make('posted_by')
                        ->default(fn () => Auth::id()),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'gray' => 'normal',
                        'warning' => 'important',
                        'danger' => 'urgent',
                    ]),

                Tables\Columns\TextColumn::make('audience')
                    ->label('Target')
                    ->badge()
                    ->color(fn (Notice $record) => match ($record->target_type) {
                        'school' => 'primary',
                        'section' => 'info',
                        'grade' => 'success',
                        'class' => 'warning',
                        'student' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('postedBy.name')
                    ->label('Posted By')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\IconColumn::make('attachment')
                    ->label('File')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->attachment)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('target_type')
                    ->label('Audience')
                    ->options([
                        'school' => 'Whole School',
                        'section' => 'Section',
                        'grade' => 'Grade',
                        'class' => 'Class',
                        'student' => 'Student',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('priority')
                    ->options(['normal' => 'Normal', 'important' => 'Important', 'urgent' => 'Urgent'])
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()?->role_id === RoleConstants::ADMIN),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->role_id === RoleConstants::ADMIN),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotices::route('/'),
            'create' => Pages\CreateNotice::route('/create'),
            'edit' => Pages\EditNotice::route('/{record}/edit'),
        ];
    }
}
