<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

/**
 * Custom Filament login page that accepts ANY of the user's identifiers
 * (email, phone number in 260… format, or username) in the same input
 * field. Parents log in with their phone number; staff continue to use
 * email; nothing is lost.
 */
class Login extends BaseLogin
{
    /**
     * Replace the default email-typed input with a free-text one labelled
     * "Email or Phone" so the browser doesn't apply email-only validation
     * (which would block phone-number logins).
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email or Phone')
            ->placeholder('e.g. 260975020473 or you@example.com')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * Build the credentials array Laravel's Auth::attempt() will use.
     * Resolution order is shape-driven so collisions don't hijack a login:
     *   - Numeric input → phone, then username (covers parents on phone-login).
     *   - Input containing '@' → email FIRST, then username. This stops a
     *     scenario where another user has the admin's email stored in their
     *     `username` column from intercepting the admin's login attempt.
     *   - Otherwise → username, then email (covers short logins like "admin").
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $input = trim((string) ($data['email'] ?? ''));

        if (preg_match('/^\+?\d{6,}$/', $input)) {
            $normalised = $this->normalisePhone($input);
            if ($normalised && User::where('phone', $normalised)->exists()) {
                return ['phone' => $normalised, 'password' => $data['password']];
            }
            if ($normalised && User::where('username', $normalised)->exists()) {
                return ['username' => $normalised, 'password' => $data['password']];
            }
        }

        if (str_contains($input, '@')) {
            if (User::where('email', $input)->exists()) {
                return ['email' => $input, 'password' => $data['password']];
            }
            if (User::where('username', $input)->exists()) {
                return ['username' => $input, 'password' => $data['password']];
            }
        }

        if (User::where('username', $input)->exists()) {
            return ['username' => $input, 'password' => $data['password']];
        }

        return [
            'email' => $input,
            'password' => $data['password'],
        ];
    }

    /**
     * Normalise any reasonable Zambian phone format to 260XXXXXXXXX (no +).
     * '+260975020473' → '260975020473'
     * '0975020473'    → '260975020473'
     * '260975020473'  → '260975020473'
     */
    protected function normalisePhone(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw);

        if ($digits === '' || $digits === null) {
            return null;
        }

        if (str_starts_with($digits, '260')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '260' . substr($digits, 1);
        }

        if (strlen($digits) >= 9 && strlen($digits) <= 10) {
            return '260' . ltrim($digits, '0');
        }

        return $digits;
    }
}
