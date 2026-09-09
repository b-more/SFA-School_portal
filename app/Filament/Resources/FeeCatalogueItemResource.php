<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeCatalogueItemResource\Pages;
use App\Models\FeeCatalogueItem;
use App\Models\FeeCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeeCatalogueItemResource extends Resource
{
    protected static ?string $model = FeeCatalogueItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Fees';
    protected static ?string $navigationLabel = 'Fee Catalogue';
    protected static ?int $navigationSort = 31;

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
            Forms\Components\Select::make('fee_category_id')
                ->label('Category')
                ->options(FeeCategory::whereIn('code', [FeeCategory::UNIFORM, FeeCategory::EDUCATIONAL_TOUR])
                    ->orderBy('sort_order')->pluck('name', 'id'))
                ->required()
                ->helperText('Catalogue items are for Uniforms or Educational Tours.'),
            Forms\Components\TextInput::make('name')->required()->maxLength(120)
                ->placeholder('e.g. "Boys Shirt — Primary" or "Lusaka Tour 2026"'),
            Forms\Components\TextInput::make('amount')->label('Amount (K)')
                ->required()->numeric()->minValue(0)->step(0.01),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fee_category_id')
            ->columns([
                Tables\Columns\TextColumn::make('category.name')->label('Category')->badge()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('ZMW'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fee_category_id')
                    ->label('Category')
                    ->options(FeeCategory::orderBy('sort_order')->pluck('name', 'id')),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageFeeCatalogueItems::route('/')];
    }
}
