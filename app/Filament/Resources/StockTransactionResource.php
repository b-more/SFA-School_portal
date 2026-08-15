<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\StockTransactionResource\Pages;
use App\Models\MedicalStockItem;
use App\Models\StockTransaction;
use App\Services\StockLedgerService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockTransactionResource extends Resource
{
    protected static ?string $model = StockTransaction::class;

    protected static ?string $navigationIcon  = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Clinic';
    protected static ?string $navigationLabel = 'Stock movements';
    protected static ?string $modelLabel      = 'Stock movement';
    protected static ?int    $navigationSort  = 4;

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
            Forms\Components\Select::make('medical_stock_item_id')
                ->label('Stock item')
                ->options(MedicalStockItem::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                ->searchable()->required()->live()
                ->helperText(function ($state) {
                    if (! $state) return null;
                    $item = MedicalStockItem::find($state);
                    return $item ? "Current balance: {$item->current_balance} {$item->unit}" : null;
                }),
            Forms\Components\Select::make('transaction_type')
                ->options([
                    'purchase'         => 'Purchase (stock IN)',
                    'opening'          => 'Opening balance (stock IN)',
                    'adjustment'       => 'Adjustment (±)',
                    'expired_damaged'  => 'Expired / damaged (stock OUT)',
                ])
                ->required()
                ->default('purchase')
                ->live(),
            Forms\Components\TextInput::make('quantity')
                ->numeric()->required()
                ->helperText('Positive number. For "Adjustment", prefix with a minus sign to subtract (e.g. -3).')
                ->minValue(fn (Forms\Get $get) => $get('transaction_type') === 'adjustment' ? -100000 : 1),
            Forms\Components\DatePicker::make('transaction_date')
                ->default(now())
                ->maxDate(now())
                ->required()
                ->native(false),
            Forms\Components\TextInput::make('unit_cost')
                ->prefix('K')->numeric()->minValue(0)
                ->helperText('Unit cost in ZMK — only for purchases')
                ->visible(fn (Forms\Get $get) => in_array($get('transaction_type'), ['purchase', 'opening'])),
            Forms\Components\TextInput::make('supplier')
                ->maxLength(120)
                ->visible(fn (Forms\Get $get) => in_array($get('transaction_type'), ['purchase', 'opening'])),
            Forms\Components\Textarea::make('notes')
                ->rows(2)
                ->columnSpanFull()
                ->required(fn (Forms\Get $get) => $get('transaction_type') === 'adjustment')
                ->helperText(fn (Forms\Get $get) => $get('transaction_type') === 'adjustment'
                    ? 'Required — explain the correction (e.g. "found extra 10 tablets during audit").'
                    : null),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('item.name')->label('Item')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('transaction_type')->badge()
                    ->color(fn ($state) => match ($state) {
                        'purchase', 'opening' => 'success',
                        'usage'               => 'primary',
                        'expired_damaged'     => 'danger',
                        'adjustment'          => 'warning',
                        default               => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->formatStateUsing(function ($state, $record) {
                        $sign = in_array($record->transaction_type, ['usage','expired_damaged']) ? '−' : '+';
                        if ($record->transaction_type === 'adjustment') $sign = $state >= 0 ? '+' : '';
                        return $sign . $state . ' ' . ($record->item?->unit ?? '');
                    }),
                Tables\Columns\TextColumn::make('line_total')->label('Cost (ZMK)')
                    ->formatStateUsing(fn ($state) => $state ? 'K' . number_format($state, 2) : '—'),
                Tables\Columns\TextColumn::make('supplier')->toggleable(),
                Tables\Columns\TextColumn::make('visit_id')->label('Visit #')->toggleable(),
                Tables\Columns\TextColumn::make('recorder.name')->label('Recorded by')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notes')->limit(40)->toggleable(),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')->options([
                    'purchase' => 'Purchase', 'opening' => 'Opening',
                    'usage' => 'Usage', 'adjustment' => 'Adjustment', 'expired_damaged' => 'Expired/Damaged',
                ]),
                Tables\Filters\SelectFilter::make('medical_stock_item_id')
                    ->label('Item')
                    ->options(MedicalStockItem::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->native(false),
                        Forms\Components\DatePicker::make('to')->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('transaction_date', '>=', $d))
                            ->when($data['to']   ?? null, fn ($q, $d) => $q->whereDate('transaction_date', '<=', $d));
                    }),
            ])
            ->actions([Tables\Actions\ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStockTransactions::route('/'),
            'create' => Pages\CreateStockTransaction::route('/create'),
            'view'   => Pages\ViewStockTransaction::route('/{record}'),
        ];
    }
}
