<?php

namespace App\Http\Controllers\Api;

use App\Constants\RoleConstants;
use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use App\Models\SchoolSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

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

        // Try email first, then username
        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('username', $login)->orWhere('email', $login)->first();

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
            ],
            'children' => $children->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'grade' => $s->grade?->name,
                'class' => $s->classSection?->name,
            ]),
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 400);
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
