<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\CpdActivityResource\Pages;
use App\Models\CpdActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CpdActivityResource extends Resource
{
    protected static ?string $model = CpdActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'CPD Activities';

    protected static ?string $navigationGroup = 'CPD Management';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role_id, array_merge([RoleConstants::ADMIN], RoleConstants::teaching())) ?? false;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role_id, array_merge([RoleConstants::ADMIN], RoleConstants::teaching())) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Activity Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Teacher')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->options([
                                'workshop' => 'Workshop',
                                'conference' => 'Conference',
                                'course' => 'Course',
                                'seminar' => 'Seminar',
                                'webinar' => 'Webinar',
                                'self_study' => 'Self Study',
                                'peer_observation' => 'Peer Observation',
                                'mentoring' => 'Mentoring',
                                'research' => 'Research',
                                'other' => 'Other',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('provider')
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('start_date')
                            ->required(),

                        Forms\Components\DatePicker::make('end_date')
                            ->after('start_date'),

                        Forms\Components\TextInput::make('hours')
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'planned' => 'Planned',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('planned')
                            ->required(),

                        Forms\Components\TextInput::make('academic_year')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('term')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Description & Reflection')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('reflection')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('key_learnings')
                            ->label('Key Learnings')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Certificate')
                    ->schema([
                        Forms\Components\FileUpload::make('certificate_file')
                            ->label('Certificate')
                            ->directory('cpd-certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('type')
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('hours')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'planned',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\IconColumn::make('certificate_file')
                    ->label('Certificate')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-minus')
                    ->getStateUsing(fn (CpdActivity $record): bool => ! empty($record->certificate_file)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'workshop' => 'Workshop',
                        'conference' => 'Conference',
                        'course' => 'Course',
                        'seminar' => 'Seminar',
                        'webinar' => 'Webinar',
                        'self_study' => 'Self Study',
                        'peer_observation' => 'Peer Observation',
                        'mentoring' => 'Mentoring',
                        'research' => 'Research',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planned' => 'Planned',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('academic_year')
                    ->options(fn () => CpdActivity::query()
                        ->whereNotNull('academic_year')
                        ->distinct()
                        ->pluck('academic_year', 'academic_year')
                        ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
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
            'index' => Pages\ListCpdActivities::route('/'),
            'create' => Pages\CreateCpdActivity::route('/create'),
            'edit' => Pages\EditCpdActivity::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['user:id,name']);
    }
}
