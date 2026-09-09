<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model            = User::class;
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationGroup  = 'User Management';
    protected static ?int    $navigationSort   = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),

                Forms\Components\Select::make('role_id')
                    ->label('Role')
                    ->options(Role::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn ($context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state)),

                Forms\Components\Select::make('status')
                    ->options([
                        'active'    => 'Active',
                        'inactive'  => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->required()
                    ->default('active'),

                Forms\Components\Toggle::make('must_change_password')
                    ->label('Force password change on next login'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('last_login_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $r) => $r->email ?: $r->username),

                Tables\Columns\TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Admin'      => 'danger',
                        'Accountant' => 'warning',
                        'Teacher'    => 'info',
                        'Parent'     => 'success',
                        'Student'    => 'gray',
                        'Clinician', 'Nurse' => 'primary',
                        default      => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'inactive'  => 'gray',
                        'suspended' => 'danger',
                        default     => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last login')
                    ->dateTime('D d M Y · H:i')
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('last_login_ip')
                    ->label('From IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Role')
                    ->options(fn () => Role::orderBy('name')->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'    => 'Active',
                        'inactive'  => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\Filter::make('ever_logged_in')
                    ->label('Has logged in')
                    ->query(fn ($query) => $query->whereNotNull('last_login_at')),
                Tables\Filters\Filter::make('never_logged_in')
                    ->label('Never logged in')
                    ->query(fn ($query) => $query->whereNull('last_login_at')),
                Tables\Filters\Filter::make('signed_in_now')
                    ->label('Signed in right now')
                    ->query(fn ($query) => $query->whereIn('id', function ($q) {
                        $q->from('sessions')->select('user_id')
                          ->whereNotNull('user_id')
                          ->where('last_activity', '>=', time() - ((int) config('session.lifetime') * 60));
                    })),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
