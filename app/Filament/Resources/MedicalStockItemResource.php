<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\MedicalStockItemResource\Pages;
use App\Models\MedicalStockItem;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MedicalStockItemResource extends Resource
{
    protected static ?string $model = MedicalStockItem::class;

    protected static ?string $navigationIcon  = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Clinic';
    protected static ?string $navigationLabel = 'Medical stock items';
    protected static ?string $modelLabel      = 'Stock item';
    protected static ?int    $navigationSort  = 2;

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN, RoleConstants::CLINICIAN,
        ], true);
    }

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(120),
            Forms\Components\Select::make('category')->options([
                'tablet'       => 'Tablet',
                'syrup'        => 'Syrup',
                'gel_ointment' => 'Gel / Ointment',
                'lozenge'      => 'Lozenge',
                'first_aid'    => 'First Aid',
                'other'        => 'Other',
            ])->required(),
            Forms\Components\TextInput::make('unit')->required()->helperText('tablets, ml, pieces, sachets…')->default('tablets'),
            Forms\Components\TextInput::make('reorder_level')->numeric()->minValue(0)->default(10)->required()
                ->helperText('Low-stock alert fires when current balance ≤ this number.'),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->sortable(),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('current_balance')
                    ->label('Balance')
                    ->badge()
                    ->color(fn ($record) => $record->is_low_stock ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state, $record) => $state . ' ' . $record->unit),
                Tables\Columns\TextColumn::make('reorder_level')->label('Re-order @'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options([
                    'tablet' => 'Tablet', 'syrup' => 'Syrup', 'gel_ointment' => 'Gel/Ointment',
                    'lozenge' => 'Lozenge', 'first_aid' => 'First Aid', 'other' => 'Other',
                ]),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low stock only')
                    ->toggle()
                    ->query(fn ($query) => $query->whereRaw('reorder_level >= (
                        SELECT COALESCE(SUM(CASE WHEN transaction_type IN ("purchase","opening") THEN quantity WHEN transaction_type = "adjustment" THEN quantity ELSE 0 END), 0)
                             - COALESCE(SUM(CASE WHEN transaction_type IN ("usage","expired_damaged") THEN quantity ELSE 0 END), 0)
                        FROM stock_transactions WHERE medical_stock_item_id = medical_stock_items.id
                    )')),
            ])
            ->defaultSort('name')
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMedicalStockItems::route('/'),
            'create' => Pages\CreateMedicalStockItem::route('/create'),
            'edit'   => Pages\EditMedicalStockItem::route('/{record}/edit'),
        ];
    }
}
