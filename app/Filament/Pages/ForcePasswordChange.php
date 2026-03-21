<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForcePasswordChange extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $title = 'Change Your Password';
    protected static ?string $slug = 'force-password-change';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.force-password-change';

    public ?array $data = [];

    public function mount(): void
    {
        if (!Auth::user()->must_change_password) {
            $this->redirect('/admin');
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->required()
                    ->rule(Password::min(8))
                    ->revealable()
                    ->helperText('Minimum 8 characters.'),

                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->required()
                    ->same('password')
                    ->revealable(),
            ])
            ->statePath('data');
    }

    public function changePassword(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        Notification::make()
            ->title('Password Changed')
            ->body('Your password has been updated successfully. Welcome!')
            ->success()
            ->send();

        $this->redirect('/admin');
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->must_change_password;
    }
}
