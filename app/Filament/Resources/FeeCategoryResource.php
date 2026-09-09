<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeCategoryResource\Pages;
use App\Models\FeeCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeeCategoryResource extends Resource
{
    protected static ?string $model = FeeCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Fees';
    protected static ?string $navigationLabel = 'Fee Categories';
    protected static ?int $navigationSort = 30;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()?->isFinanceLocked()) return false;
        return in_array(auth()->user()?->role_id, [
            \App\Constants\RoleConstants::ADMIN,
            \App\Constants\RoleConstants::ACCOUNTANT,
            \App\Constants\RoleConstants::DIRECTOR,
        ], true);
    }

    public static function canViewAny(): bool { return self::shouldRegisterNavigation(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->required()->disabled()->dehydrated(false)
                ->helperText('System code — edit name and amount instead.'),
            Forms\Components\TextInput::make('name')->required()->maxLength(64),
            Forms\Components\Select::make('frequency')
                ->options([
                    'term'    => 'Per term',
                    'monthly' => 'Monthly',
                    'annual'  => 'Annual',
                    'one_off' => 'One-off (ad-hoc / catalogue)',
                ])->required(),
            Forms\Components\TextInput::make('default_amount')->label('Default amount (K)')
                ->numeric()->minValue(0)->step(0.01)
                ->helperText('Used when generating fees (e.g. K500 for Bus, K100 for PTA).'),
            Forms\Components\TextInput::make('narration_template')
                ->label('SMS / receipt narration template')
                ->required()
                ->helperText('Use {category} and {period}. Example: "{category} ({period})"'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->badge(),
                Tables\Columns\TextColumn::make('frequency')->badge(),
                Tables\Columns\TextColumn::make('default_amount')->money('ZMW')->label('Default'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageFeeCategories::route('/')];
    }
}
