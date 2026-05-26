<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\DriverResource\Pages;
use App\Models\BusFareStructure;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DriverResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'drivers';

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Finance Management';

    protected static ?string $navigationLabel = 'Bus Drivers';

    protected static ?string $modelLabel = 'Driver';

    protected static ?string $pluralModelLabel = 'Drivers';

    protected static ?int $navigationSort = 7;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role_id', RoleConstants::DRIVER)
            ->withCount('busRoutes');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Driver Profile')
                    ->description('Personal details and contact information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('+260...')
                            ->helperText('Used to send login SMS and for parents to contact')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->unique(table: User::class, column: 'email', ignoreRecord: true)
                            ->helperText('Auto-generated if left blank')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('username')
                            ->label('Username')
                            ->maxLength(255)
                            ->unique(table: User::class, column: 'username', ignoreRecord: true)
                            ->helperText('Optional — driver can log in with email')
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active (can log in)',
                                'inactive' => 'Inactive (cannot log in)',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2)
                            ->placeholder('Anything to remember about this driver')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Route Assignment')
                    ->description('Routes this driver is responsible for. They will see paid-up students on these routes.')
                    ->schema([
                        Forms\Components\Select::make('route_ids')
                            ->label('Assigned Routes')
                            ->multiple()
                            ->options(function ($record) {
                                $query = BusFareStructure::query()->where('is_active', true);

                                if ($record) {
                                    $query->where(function ($q) use ($record) {
                                        $q->whereNull('driver_user_id')
                                            ->orWhere('driver_user_id', $record->id);
                                    });
                                } else {
                                    $query->whereNull('driver_user_id');
                                }

                                return $query->orderBy('route_name')
                                    ->pluck('route_name', 'id')
                                    ->toArray();
                            })
                            ->preload()
                            ->searchable()
                            ->optionsLimit(200)
                            ->helperText('Only unassigned routes (or this driver\'s current routes) are shown. To re-assign a route from another driver, edit that driver first.')
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->copyMessage('Phone copied'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bus_routes_count')
                    ->label('Routes')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state . ' ' . str('route')->plural($state)),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'suspended',
                    ]),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset driver password')
                    ->modalDescription('Generates a new temporary password and forces the driver to change it on next login.')
                    ->action(function (User $record) {
                        $password = \Illuminate\Support\Str::password(10, symbols: false);
                        $record->update([
                            'password' => \Illuminate\Support\Facades\Hash::make($password),
                            'must_change_password' => true,
                        ]);

                        \App\Models\UserCredential::create([
                            'user_id' => $record->id,
                            'username' => $record->email ?? $record->username,
                            'password' => $password,
                            'is_sent' => false,
                            'delivery_method' => 'manual',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Password reset')
                            ->body("New password for {$record->name}: {$password}")
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'active']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['status' => 'inactive']))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('No Drivers Yet')
            ->emptyStateDescription('Add your first bus driver to get started.')
            ->emptyStateIcon('heroicon-o-identification');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'view' => Pages\ViewDriver::route('/{record}'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
