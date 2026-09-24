<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\BotConversationResource\Pages;
use App\Models\BotConversation;
use App\Models\ParentGuardian;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BotConversationResource extends Resource
{
    protected static ?string $model = BotConversation::class;

    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'WhatsApp Bot Log';
    protected static ?string $modelLabel = 'WhatsApp conversation';
    protected static ?string $pluralModelLabel = 'WhatsApp conversations';
    protected static ?int $navigationSort = 15;

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id ?? 0, [RoleConstants::ADMIN, RoleConstants::DIRECTOR], true);
    }

    /**
     * The list page is grouped by phone: one row per unique sender, showing
     * activity summary. We use a subquery so Filament's paginator sees an
     * aggregated "id" it can key rows by.
     */
    public static function getEloquentQuery(): Builder
    {
        $sub = DB::table('bot_conversations')
            ->select(
                DB::raw('MAX(id) AS id'),
                'phone',
                'guardian_id',
                DB::raw('COUNT(*) AS message_count'),
                DB::raw('SUM(direction = "in")  AS inbound_count'),
                DB::raw('SUM(direction = "out") AS outbound_count'),
                DB::raw('MAX(created_at) AS last_activity'),
                DB::raw('MIN(created_at) AS first_seen'),
            )
            ->groupBy('phone', 'guardian_id');

        return BotConversation::query()
            ->fromSub($sub, 'bot_conversations')
            ->orderByDesc('last_activity');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (BotConversation $r) => static::getUrl('view', ['record' => $r->id]))
            ->defaultSort('last_activity', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('display_phone')
                    ->label('Sender')
                    ->searchable(query: fn (Builder $q, $s) => $q->where('phone', 'like', '%' . preg_replace('/\D+/', '', $s) . '%'))
                    ->weight('bold')
                    ->description(function (BotConversation $r) {
                        if (! $r->guardian_id) {
                            return 'Not linked to a parent record';
                        }
                        $name = ParentGuardian::whereKey($r->guardian_id)->value('name');
                        return $name ? '👤 ' . $name : 'Guardian #' . $r->guardian_id;
                    }),
                Tables\Columns\TextColumn::make('message_count')
                    ->label('Messages')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('inbound_count')
                    ->label('From parent')
                    ->numeric()
                    ->color('gray')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('outbound_count')
                    ->label('From bot')
                    ->numeric()
                    ->color('success')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last activity')
                    ->dateTime('d M Y, H:i')
                    ->description(fn (BotConversation $r) => $r->last_activity ? \Carbon\Carbon::parse($r->last_activity)->diffForHumans() : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_seen')
                    ->label('First seen')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('linked')
                    ->label('Linked to a parent')
                    ->query(fn (Builder $q) => $q->whereNotNull('guardian_id')),
                Tables\Filters\Filter::make('unlinked')
                    ->label('Not linked (unknown senders)')
                    ->query(fn (Builder $q) => $q->whereNull('guardian_id')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View conversation')
                    ->icon('heroicon-o-chat-bubble-oval-left')
                    ->url(fn (BotConversation $r) => static::getUrl('view', ['record' => $r->id])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBotConversations::route('/'),
            'view'  => Pages\ViewBotConversation::route('/{record}'),
        ];
    }
}
