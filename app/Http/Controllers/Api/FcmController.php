<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * FCM device-token registration for the parent + teacher Android apps.
 * The actual notification sending lives in a separate service (added when
 * google-services.json + Firebase service account JSON are in place).
 */
class FcmController extends Controller
{
    public function registerParent(Request $request)
    {
        return $this->register($request, 'parent');
    }

    public function registerTeacher(Request $request)
    {
        return $this->register($request, 'teacher');
    }

    private function register(Request $request, string $app)
    {
        $data = $request->validate([
            'token' => 'required|string|min:30|max:4000',
            'device_label' => 'nullable|string|max:200',
        ]);

        FcmToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id' => Auth::id(),
                'app' => $app,
                'device_label' => $data['device_label'] ?? null,
                'last_active_at' => now(),
            ]
        );

        return response()->json(['message' => 'Registered.']);
    }

    public function unregister(Request $request)
    {
        $data = $request->validate(['token' => 'required|string']);
        FcmToken::where('token', $data['token'])->delete();
        return response()->json(['message' => 'Unregistered.']);
    }
}
