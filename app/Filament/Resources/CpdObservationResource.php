<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\CpdObservationResource\Pages;
use App\Models\CpdObservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CpdObservationResource extends Resource
{
    protected static ?string $model = CpdObservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'CPD Observations';

    protected static ?string $navigationGroup = 'CPD Management';

    protected static ?int $navigationSort = 4;

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
                Forms\Components\Section::make('Observation Details')
                    ->schema([
                        Forms\Components\Select::make('teacher_user_id')
                            ->label('Teacher')
                            ->relationship('teacher', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('observer_user_id')
                            ->label('Observer')
                            ->relationship('observer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('observation_date')
                            ->label('Date')
                            ->required(),

                        Forms\Components\TextInput::make('subject')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('class_observed')
                            ->label('Class')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('topic')
                            ->maxLength(255),

                        Forms\Components\Select::make('rating')
                            ->options([
                                1 => '1 - Needs Improvement',
                                2 => '2 - Developing',
                                3 => '3 - Competent',
                                4 => '4 - Proficient',
                                5 => '5 - Outstanding',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Feedback')
                    ->schema([
                        Forms\Components\Textarea::make('strengths')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('areas_for_improvement')
                            ->label('Areas for Improvement')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('recommendations')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('teacher_reflection')
                            ->label('Teacher Reflection')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('observer.name')
                    ->label('Observer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('observation_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('class_observed')
                    ->label('Class')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('rating')
                    ->colors([
                        'danger' => 1,
                        'warning' => 2,
                        'primary' => 3,
                        'success' => fn (int $state): bool => $state >= 4,
                    ])
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => '1 - Needs Improvement',
                        2 => '2 - Developing',
                        3 => '3 - Competent',
                        4 => '4 - Proficient',
                        5 => '5 - Outstanding',
                        default => (string) $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 - Needs Improvement',
                        2 => '2 - Developing',
                        3 => '3 - Competent',
                        4 => '4 - Proficient',
                        5 => '5 - Outstanding',
                    ]),
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
            ->defaultSort('observation_date', 'desc');
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
            'index' => Pages\ListCpdObservations::route('/'),
            'create' => Pages\CreateCpdObservation::route('/create'),
            'edit' => Pages\EditCpdObservation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['teacher:id,name', 'observer:id,name']);
    }
}
