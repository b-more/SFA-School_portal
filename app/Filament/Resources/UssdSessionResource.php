<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\UssdSessionResource\Pages;
use App\Models\UssdSession;
use App\Models\ParentGuardian;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UssdSessionResource extends Resource
{
    protected static ?string $model = UssdSession::class;

    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-down-left';
    protected static ?string $navigationLabel = 'USSD Log';
    protected static ?string $modelLabel = 'USSD session';
    protected static ?string $pluralModelLabel = 'USSD sessions';
    protected static ?int $navigationSort = 16;

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id ?? 0, [RoleConstants::ADMIN, RoleConstants::DIRECTOR], true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (UssdSession $r) => static::getUrl('view', ['record' => $r->id]))
            ->columns([
                Tables\Columns\TextColumn::make('display_msisdn')
                    ->label('Caller')
                    ->searchable(query: fn (Builder $q, $s) => $q->where('msisdn', 'like', '%' . preg_replace('/\D+/', '', $s) . '%'))
                    ->weight('bold')
                    ->description(function (UssdSession $r) {
                        if (! $r->guardian_id) {
                            return 'Not linked to a parent record';
                        }
                        $name = ParentGuardian::whereKey($r->guardian_id)->value('name');
                        return $name ? '👤 ' . $name : 'Guardian #' . $r->guardian_id;
                    }),
                Tables\Columns\TextColumn::make('shortcode')
                    ->label('Dialed')
                    ->placeholder('—')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('state')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'START'         => 'gray',
                        'PICK_STUDENT'  => 'primary',
                        'STUDENT_MENU'  => 'primary',
                        'PAY_AMOUNT'    => 'warning',
                        'PAY_CONFIRM'   => 'warning',
                        default         => 'gray',
                    }),
                Tables\Columns\TextColumn::make('transcript_count')
                    ->label('Keystrokes')
                    ->state(fn (UssdSession $r) => is_array($r->transcript) ? count($r->transcript) : 0)
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('ended')
                    ->label('Ended')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Started')
                    ->dateTime('d M Y, H:i:s')
                    ->description(fn (UssdSession $r) => $r->created_at?->diffForHumans())
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('linked')
                    ->label('Linked to a parent')
                    ->query(fn (Builder $q) => $q->whereNotNull('guardian_id')),
                Tables\Filters\Filter::make('unlinked')
                    ->label('Not linked (unknown callers)')
                    ->query(fn (Builder $q) => $q->whereNull('guardian_id')),
                Tables\Filters\Filter::make('active')
                    ->label('Still active')
                    ->query(fn (Builder $q) => $q->where('ended', false)),
                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $q) => $q->whereDate('created_at', today())),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View session')
                    ->icon('heroicon-o-eye')
                    ->url(fn (UssdSession $r) => static::getUrl('view', ['record' => $r->id])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUssdSessions::route('/'),
            'view'  => Pages\ViewUssdSession::route('/{record}'),
        ];
    }
}
