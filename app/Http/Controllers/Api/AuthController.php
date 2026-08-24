<?php

namespace App\Http\Controllers\Api;

use App\Constants\RoleConstants;
use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use App\Models\SchoolSettings;
use App\Models\User;
use App\Services\SmsService;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($validated['login']);
        $password = $validated['password'];

        $user = $this->resolveLogin($login);

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['message' => 'The email/username or password is incorrect.'], 401);
        }

        if ($user->role_id !== RoleConstants::PARENT) {
            return response()->json(['message' => 'This app is for parents only. Please use the main portal.'], 403);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Your account has been deactivated. Contact the school.'], 403);
        }

        $user->update(['last_login' => now()]);

        // Revoke old tokens and issue new one
        $user->tokens()->delete();
        $token = $user->createToken('parent-mobile')->plainTextToken;

        $parent = ParentGuardian::where('user_id', $user->id)->first();
        $children = $parent?->students()->where('enrollment_status', 'active')->with(['grade', 'classSection'])->get() ?? [];

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $parent->name ?? $user->name,
                'email' => $parent->email ?? $user->email,
                'phone' => $parent->phone ?? null,
                'relationship' => $parent->relationship ?? null,
                'profile_photo' => $this->profilePhotoUrl($user),
                'must_change_password' => (bool) $user->must_change_password,
            ],
            'children' => $children->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'grade' => $s->grade?->name,
                'class' => $s->classSection?->name,
            ]),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $parent = ParentGuardian::where('user_id', $user->id)->first();
        $children = $parent?->students()->where('enrollment_status', 'active')->with(['grade', 'classSection'])->get() ?? collect();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $parent->name ?? $user->name,
                'email' => $parent->email ?? $user->email,
                'phone' => $parent->phone ?? null,
                'relationship' => $parent->relationship ?? null,
                'profile_photo' => $this->profilePhotoUrl($user),
            ],
            'children' => $children->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'grade' => $s->grade?->name,
                'class' => $s->classSection?->name,
            ]),
        ]);
    }

    /**
     * Upload or replace the current user's profile photo.
     * Returns the new public URL.
     */
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|image|mimes:jpeg,jpg,png,webp|max:5120', // 5 MB
        ]);

        $user = $request->user();
        $file = $request->file('photo');

        // Remove previous photo if any
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $filename = 'photo-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("users/{$user->id}", $filename, 'public');

        $user->update(['profile_photo_path' => $path]);

        return response()->json([
            'message' => 'Profile photo updated.',
            'profile_photo' => $this->profilePhotoUrl($user->fresh()),
        ]);
    }

    /**
     * Remove the current user's profile photo.
     */
    public function deleteProfilePhoto(Request $request)
    {
        $user = $request->user();
        if ($user->profile_photo_path) {
            if (Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->update(['profile_photo_path' => null]);
        }
        return response()->json(['message' => 'Profile photo removed.', 'profile_photo' => null]);
    }

    private function profilePhotoUrl(?User $user): ?string
    {
        if (!$user || !$user->profile_photo_path) return null;
        return '/storage/' . ltrim($user->profile_photo_path, '/');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 400);
    }

    /**
     * Resolve a login identifier to a User. Tries email, then username,
     * then a normalized phone-number match. Returns null if nothing matches.
     */
    private function resolveLogin(string $login): ?User
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $u = User::where('email', $login)->first();
            if ($u) return $u;
        }

        $u = User::where('username', $login)->orWhere('email', $login)->first();
        if ($u) return $u;

        $phone = PhoneNormalizer::normalize($login);
        if ($phone) {
            $u = User::where('phone', $phone)->first();
            if ($u) return $u;

            // Fallback: match against ParentGuardian.alternate_phone.
            // The primary lookups above always win when the number is
            // somebody's real login, so this branch only fires for numbers
            // that are only ever recorded as an alternate. Normalise both
            // sides — stored alternates can be raw ("0975020473"), local
            // ("260…"), or E.164 ("+260…"). If two active parents share the
            // same alternate we refuse rather than guess.
            $candidates = ParentGuardian::whereNotNull('user_id')
                ->whereNotNull('alternate_phone')
                ->where('alternate_phone', '!=', '')
                ->get(['id', 'user_id', 'alternate_phone']);

            $matched = $candidates->filter(function ($p) use ($phone) {
                return PhoneNormalizer::normalize($p->alternate_phone) === $phone;
            });

            if ($matched->count() === 1) {
                $u = User::where('id', $matched->first()->user_id)
                    ->where('status', 'active')
                    ->first();
                if ($u) return $u;
            } elseif ($matched->count() > 1) {
                Log::warning('Login by alternate_phone matched multiple parents — refusing to guess.', [
                    'phone' => $phone,
                    'parent_ids' => $matched->pluck('id')->all(),
                ]);
            }
        }

        return null;
    }

    /**
     * Step 1 of SMS reset — accepts a phone, emits an OTP via SMS.
     * Always returns success (no user enumeration leak) and rate-limits per phone.
     */
    public function forgotPasswordSms(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        $phone = PhoneNormalizer::normalize($request->input('phone'));

        // Generic success even when the input is junk — defeats enumeration.
        $generic = response()->json([
            'message' => 'If that phone is on file, an SMS with a 6-digit code is on its way.',
        ]);

        if (! $phone) return $generic;

        // Per-phone throttle: max 3 OTP sends per 15 minutes.
        $key = 'forgot-otp:' . $phone;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return $generic;
        }
        RateLimiter::hit($key, 900);

        $user = User::where('phone', $phone)->where('status', 'active')->first();
        if (! $user) return $generic;

        // Generate + store OTP (hash only — plaintext only ever exists in this scope).
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        DB::table('password_reset_otps')->where('phone', $phone)->delete();
        DB::table('password_reset_otps')->insert([
            'phone' => $phone,
            'code_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            app(SmsService::class)->send(
                "St Francis of Assisi: Your password reset code is {$otp}. It expires in 15 minutes. Don't share it with anyone.",
                $phone,
                'auth',
                $user->id
            );
        } catch (\Throwable $e) {
            Log::warning('OTP SMS failed', ['phone' => $phone, 'error' => $e->getMessage()]);
        }

        return $generic;
    }

    /**
     * Step 2 of SMS reset — verify the OTP and set the new password.
     */
    public function resetPasswordSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        $phone = PhoneNormalizer::normalize($request->input('phone'));
        $genericFail = response()->json(['message' => 'Invalid or expired reset code.'], 422);
        if (! $phone) return $genericFail;

        $record = DB::table('password_reset_otps')
            ->where('phone', $phone)
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();
        if (! $record) return $genericFail;

        if ($record->attempts >= 5) return $genericFail;

        DB::table('password_reset_otps')->where('id', $record->id)->increment('attempts');

        if (! Hash::check($request->input('otp'), $record->code_hash)) {
            return $genericFail;
        }

        $user = User::where('phone', $phone)->where('status', 'active')->first();
        if (! $user) return $genericFail;

        $user->forceFill([
            'password' => Hash::make($request->input('new_password')),
            'must_change_password' => false,
        ])->save();

        DB::table('password_reset_otps')->where('phone', $phone)->delete();
        // Invalidate existing tokens — force re-login with the new password.
        $user->tokens()->delete();

        return response()->json(['message' => 'Password updated. Please log in with your new password.']);
    }

    /**
     * Logged-in user changes their own password — used by the forced
     * first-login prompt and the regular "change password" flow.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:current_password',
        ]);

        $user = $request->user();
        if (! Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->forceFill([
            'password' => Hash::make($request->input('new_password')),
            'must_change_password' => false,
        ])->save();

        return response()->json(['message' => 'Password updated.']);
    }

    public function schoolSettings()
    {
        $settings = SchoolSettings::first();

        return response()->json([
            'name' => $settings->school_name ?? 'St. Francis of Assisi Private School',
            'motto' => $settings->motto ?? 'Excellence in Education',
            'logo' => $settings->school_logo ? url('storage/' . $settings->school_logo) : null,
            'logo_path' => $settings->school_logo ? 'storage/' . $settings->school_logo : null,
            'phone' => $settings->phone ?? '',
            'email' => $settings->email ?? '',
        ]);
    }
}
