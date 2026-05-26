<?php

namespace App\Http\Controllers\Api;

use App\Constants\RoleConstants;
use App\Http\Controllers\Controller;
use App\Models\SchoolSettings;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class TeacherAuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($validated['login']);
        $password = $validated['password'];

        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('username', $login)->orWhere('email', $login)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['message' => 'The email/username or password is incorrect.'], 401);
        }

        // Allow teaching roles
        $allowedRoles = RoleConstants::teachingWithAdmin();
        if (!in_array($user->role_id, $allowedRoles)) {
            return response()->json(['message' => 'This app is for teachers only.'], 403);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Your account has been deactivated. Contact administration.'], 403);
        }

        $user->update(['last_login' => now()]);

        $user->tokens()->where('name', 'teacher-mobile')->delete();
        $token = $user->createToken('teacher-mobile')->plainTextToken;

        $teacher = Teacher::where('user_id', $user->id)->where('is_active', true)->first();

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $teacher?->name ?? $user->name,
                'email' => $teacher?->email ?? $user->email,
                'phone' => $teacher?->phone ?? $user->phone,
                'role_id' => $user->role_id,
                'role_name' => $user->role?->name ?? '',
                'teacher_id' => $teacher?->id,
                'is_class_teacher' => (bool) $teacher?->is_class_teacher,
                'is_grade_teacher' => (bool) $teacher?->is_grade_teacher,
                'is_head_teacher' => in_array($user->role_id, [\App\Constants\RoleConstants::HEAD_TEACHER_PRIMARY, \App\Constants\RoleConstants::HEAD_TEACHER_SECONDARY]),
                'is_deputy_head' => in_array($user->role_id, [\App\Constants\RoleConstants::DEPUTY_HEAD_PRIMARY, \App\Constants\RoleConstants::DEPUTY_HEAD_SECONDARY]),
                'is_dean' => in_array($user->role_id, [\App\Constants\RoleConstants::DEAN_OF_PRIMARY, \App\Constants\RoleConstants::DEAN_OF_SECONDARY]),
                'is_admin' => $user->role_id === \App\Constants\RoleConstants::ADMIN,
                'grade' => $teacher?->grade?->name,
                'class_section' => $teacher?->classSection?->name,
                'qualification' => $teacher?->qualification,
                'specialization' => $teacher?->specialization,
                'profile_photo' => $teacher?->profile_photo ? '/storage/' . $teacher->profile_photo : null,
                'must_change_password' => (bool) $user->must_change_password,
            ],
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
        $teacher = Teacher::where('user_id', $user->id)->with(['grade', 'classSection'])->first();

        return response()->json([
            'id' => $user->id,
            'name' => $teacher?->name ?? $user->name,
            'email' => $teacher?->email ?? $user->email,
            'phone' => $teacher?->phone ?? $user->phone,
            'teacher_id' => $teacher?->id,
            'is_class_teacher' => (bool) $teacher?->is_class_teacher,
            'is_grade_teacher' => (bool) $teacher?->is_grade_teacher,
            'grade' => $teacher?->grade?->name,
            'class_section' => $teacher?->classSection?->name,
            'profile_photo' => $teacher?->profile_photo ? '/storage/' . $teacher->profile_photo : null,
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
