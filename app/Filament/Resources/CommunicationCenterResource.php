<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunicationCenterResource\Pages;
use App\Models\MessageBroadcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Constants\RoleConstants;
use Illuminate\Support\Facades\Auth;

class CommunicationCenterResource extends Resource
{
    protected static ?string $model = MessageBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Communication Center';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function shouldRegisterNavigation(): bool
    {
        $roleId = auth()->user()?->role_id;
        return in_array($roleId, [
            RoleConstants::ADMIN,
            RoleConstants::SCHOOL_SECRETARY,
            RoleConstants::TEACHER,
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (!$user || $user->role_id === RoleConstants::ADMIN) {
            return null;
        }

        $count = MessageBroadcast::unreadCountFor($user->id);
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('total_recipients')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                Forms\Components\TextInput::make('sent_count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                Forms\Components\TextInput::make('failed_count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),
                Forms\Components\TextInput::make('total_cost')
                    ->numeric()
                    ->prefix('ZMW')
                    ->default(0)
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sending' => 'Sending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->default('draft')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isAdmin = $user?->role_id === RoleConstants::ADMIN;

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($user, $isAdmin) {
                if (!$isAdmin) {
                    // Teachers/staff only see completed broadcasts targeted to staff
                    $query->where('status', 'completed')
                        ->whereIn('recipient_scope', ['all_staff', 'teachers']);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->weight(function (MessageBroadcast $record) use ($user) {
                        if (!$user || $user->role_id === RoleConstants::ADMIN) {
                            return null;
                        }
                        return $record->isReadBy($user->id) ? null : 'bold';
                    })
                    ->icon(function (MessageBroadcast $record) use ($user) {
                        if (!$user || $user->role_id === RoleConstants::ADMIN) {
                            return null;
                        }
                        return $record->isReadBy($user->id) ? null : 'heroicon-s-envelope';
                    })
                    ->iconColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->label('Date'),

                Tables\Columns\TextColumn::make('recipient_scope')
                    ->label('Audience')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'parents' => 'Parents (SMS)',
                        'all_staff' => 'All Staff',
                        'teachers' => 'Teachers',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->color(fn (string $state) => match ($state) {
                        'parents' => 'info',
                        'all_staff' => 'success',
                        'teachers' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Recipients')
                    ->numeric()
                    ->sortable()
                    ->visible($isAdmin),

                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Sent')
                    ->numeric()
                    ->visible($isAdmin),

                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Progress')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->color(fn (int $state): string => match (true) {
                        $state < 30 => 'danger',
                        $state < 70 => 'warning',
                        default => 'success',
                    })
                    ->visible($isAdmin),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->visible($isAdmin),

                Tables\Columns\TextColumn::make('total_cost')
                    ->money('ZMW')
                    ->sortable()
                    ->visible($isAdmin),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('From')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sending' => 'Sending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->visible($isAdmin),

                Tables\Filters\SelectFilter::make('recipient_scope')
                    ->label('Audience')
                    ->options([
                        'parents' => 'Parents (SMS)',
                        'all_staff' => 'All Staff',
                        'teachers' => 'Teachers',
                    ])
                    ->visible($isAdmin),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('continue')
                    ->label('Continue Sending')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->url(fn (MessageBroadcast $record): string => route('filament.admin.resources.communication-centers.send-broadcast', $record))
                    ->visible(fn (MessageBroadcast $record): bool => $isAdmin && $record->status === 'sending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible($isAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageBroadcasts::route('/'),
            'create' => Pages\CreateBroadcast::route('/create'),
            'view' => Pages\ViewBroadcast::route('/{record}'),
            'send-broadcast' => Pages\SendBroadcast::route('/{record}/send'),
        ];
    }
}
