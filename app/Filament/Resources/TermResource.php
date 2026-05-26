<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\TermResource\Pages;
use App\Models\AcademicYear;
use App\Models\Term;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Academic Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Terms';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(AcademicYear::orderBy('start_date', 'desc')->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Term 1'),

                Forms\Components\DatePicker::make('start_date')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->after('start_date'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Set as active term')
                    ->helperText('Activating this term will deactivate other terms in the same academic year.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(AcademicYear::orderBy('start_date', 'desc')->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active only'),
            ])
            ->actions([
                Tables\Actions\Action::make('setActive')
                    ->label('Set Active')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Term $record) => "Activate {$record->name}?")
                    ->modalDescription('All new homework, results, attendance, and submissions will be tagged to this term. Other terms in the same academic year will be deactivated.')
                    ->action(function (Term $record) {
                        $record->update(['is_active' => true]);

                        Notification::make()
                            ->title('Term activated')
                            ->body($record->academicYear?->name . ' — ' . $record->name . ' is now the active term.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Term $record) => ! $record->is_active),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTerms::route('/'),
            'create' => Pages\CreateTerm::route('/create'),
            'edit' => Pages\EditTerm::route('/{record}/edit'),
        ];
    }
}
