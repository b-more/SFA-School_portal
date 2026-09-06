<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model            = AuditLog::class;
    protected static ?string $navigationIcon   = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup  = 'System';
    protected static ?string $navigationLabel  = 'Login Trail';
    protected static ?string $pluralModelLabel = 'Login Trail';
    protected static ?int    $navigationSort   = 90;

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }
    public static function canCreate(): bool  { return false; }
    public static function canEdit($record): bool  { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('D d M Y · H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login'          => 'success',
                        'logout'         => 'gray',
                        'failed_login'   => 'warning',
                        'lockout'        => 'danger',
                        'password_reset' => 'info',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucfirst($state))),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->description(fn ($record) => $record->user?->email ?? $record->user?->username ?? ($record->user_id ? "user #{$record->user_id}" : '—')),

                Tables\Columns\TextColumn::make('attempted_identifier')
                    ->label('Attempted as')
                    ->getStateUsing(fn ($record) => $record->new_values['attempted_identifier'] ?? null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('From IP')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User agent')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')->options([
                    'login'          => 'Login',
                    'logout'         => 'Logout',
                    'failed_login'   => 'Failed login',
                    'lockout'        => 'Lockout',
                    'password_reset' => 'Password reset',
                ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->native(false),
                        \Filament\Forms\Components\DatePicker::make('to')->native(false),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['to']   ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
                    ),
            ])
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
