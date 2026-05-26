<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\CpdGoalResource\Pages;
use App\Models\CpdGoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CpdGoalResource extends Resource
{
    protected static ?string $model = CpdGoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'CPD Goals';

    protected static ?string $navigationGroup = 'CPD Management';

    protected static ?int $navigationSort = 2;

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
                Forms\Components\Section::make('Goal Details')
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

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('term')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('academic_year')
                            ->maxLength(20),

                        Forms\Components\Select::make('status')
                            ->options([
                                'not_started' => 'Not Started',
                                'in_progress' => 'In Progress',
                                'achieved' => 'Achieved',
                                'deferred' => 'Deferred',
                            ])
                            ->default('not_started')
                            ->required(),

                        Forms\Components\DatePicker::make('target_date'),

                        Forms\Components\DatePicker::make('achieved_date')
                            ->after('target_date'),

                        Forms\Components\Textarea::make('evidence')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
                    ->limit(40),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'not_started',
                        'primary' => 'in_progress',
                        'success' => 'achieved',
                        'warning' => 'deferred',
                    ]),

                Tables\Columns\TextColumn::make('target_date')
                    ->label('Target Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('achieved_date')
                    ->label('Achieved Date')
                    ->date()
                    ->sortable()
                    ->placeholder('Not yet achieved'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'not_started' => 'Not Started',
                        'in_progress' => 'In Progress',
                        'achieved' => 'Achieved',
                        'deferred' => 'Deferred',
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
            ->defaultSort('target_date', 'desc');
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
            'index' => Pages\ListCpdGoals::route('/'),
            'create' => Pages\CreateCpdGoal::route('/create'),
            'edit' => Pages\EditCpdGoal::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['user:id,name']);
    }
}
