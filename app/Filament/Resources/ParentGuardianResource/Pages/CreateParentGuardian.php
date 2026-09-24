<?php

namespace App\Filament\Resources\ParentGuardianResource\Pages;

use App\Constants\RoleConstants;
use App\Filament\Resources\ParentGuardianResource;
use App\Models\User;
use App\Models\UserCredential;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CreateParentGuardian extends CreateRecord
{
    protected static string $resource = ParentGuardianResource::class;

    /**
     * Handle the creation of a new parent/guardian and a corresponding user account
     */
    /**
     * Default password applied to every newly-created parent account.
     * Forces a change on first login (must_change_password = true).
     */
    protected const DEFAULT_PASSWORD = 'School1234!';

    protected function handleRecordCreation(array $data): Model
    {
        // Wrap in a transaction to ensure both parent and user are created or neither
        return DB::transaction(function () use ($data) {
            // Default password (parent will be forced to change on first login).
            $password = self::DEFAULT_PASSWORD;

            // Phone normalised to 260XXXXXXXXX — this becomes the username so parents
            // can log in with their phone number per the school's policy.
            $internationalPhone = $this->formatPhoneNumber($data['phone']);

            // Pick the username: international phone if it's available, else a
            // sanitised email-style fallback so duplicates don't blow up the insert.
            $username = $this->pickUsername($internationalPhone, $data['name']);

            // Create a new user for this parent/guardian
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'username' => $username,
                'password' => Hash::make($password),
                'role_id' => RoleConstants::PARENT,
                'status' => 'active',
                'must_change_password' => true,
            ]);

            // Create the parent/guardian and link it to the user
            $parentGuardian = static::getModel()::create(array_merge(
                $data,
                ['user_id' => $user->id]
            ));

            // Send the login credentials via SMS
            try {
                $message = "Hello {$data['name']}, your St Francis parent portal account is ready. "
                    . "Username: {$username} | Password: {$password} . "
                    . "Login at " . config('app.url') . "/admin/login and change the password on first login.";

                $formattedPhone = $internationalPhone;
                $this->sendMessage($message, $formattedPhone);

                // Record successful SMS sending
                UserCredential::create([
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'password' => $password, // Store temporary plain text password
                    'is_sent' => true,
                    'sent_at' => now(),
                    'delivery_method' => 'sms',
                ]);

                // Log the successful SMS send (without the password)
                Log::info('Parent/Guardian credentials sent via SMS', [
                    'parent_guardian_id' => $parentGuardian->id,
                    'phone' => $data['phone'],
                    'username' => $user->username
                ]);

                // Show success notification
                Notification::make()
                    ->title('Parent portal account created')
                    ->body("Login credentials sent to {$data['phone']}. Please verify with the parent that they received the SMS.")
                    ->success()
                    ->send();

            } catch (\Exception $e) {
                // Store the credentials for manual retrieval later
                UserCredential::create([
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'password' => $password, // Store temporary plain text password
                    'is_sent' => false,
                    'delivery_method' => 'manual',
                ]);

                // Log the error but don't fail the transaction
                Log::error('Failed to send parent/guardian credentials via SMS', [
                    'parent_guardian_id' => $parentGuardian->id,
                    'error' => $e->getMessage()
                ]);

                // Notify the admin of the SMS failure
                Notification::make()
                    ->title('Parent portal account created')
                    ->body("Warning: Failed to send credentials to {$data['phone']}. The credentials have been stored for manual retrieval.")
                    ->warning()
                    ->send();
            }

            return $parentGuardian;
        });
    }

    /**
     * Pick the right username for a new parent.
     * Preference order:
     *   1. International phone (260…) if it's not already taken
     *   2. Sanitised email-style fallback derived from name (kept for resilience
     *      when two parents share a phone — same convention as the bulk reset).
     */
    protected function pickUsername(?string $internationalPhone, string $name): string
    {
        if ($internationalPhone && ! User::where('username', $internationalPhone)->exists()) {
            return $internationalPhone;
        }

        // Fallback: build something stable from the name.
        $baseUsername = strtolower(str_replace(' ', '.', $name));
        $baseUsername = preg_replace('/[^a-z0-9\.]/', '', $baseUsername);
        $baseUsername = $baseUsername ?: 'parent';

        $candidate = $baseUsername . '@stfrancisofassisizm.com';
        $counter = 1;

        while (User::where('username', $candidate)->exists()) {
            $candidate = $baseUsername . $counter . '@stfrancisofassisizm.com';
            $counter++;
        }

        return $candidate;
    }

    /**
     * Format phone number to ensure it has the country code
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Check if number already has country code (260 for Zambia)
        if (substr($phoneNumber, 0, 3) === '260') {
            // Number already has country code
            return $phoneNumber;
        }

        // If starting with 0, replace with country code
        if (substr($phoneNumber, 0, 1) === '0') {
            return '260' . substr($phoneNumber, 1);
        }

        // If number doesn't have country code, add it
        if (strlen($phoneNumber) === 9) {
            return '260' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Send a message via SMS
     */
    protected function sendMessage($message_string, $phone_number)
    {
        try {
            // Log the sending attempt
            Log::info('Sending SMS notification', [
                'phone' => $phone_number,
                'message' => substr($message_string, 0, 30) . '...' // Only log beginning of message for privacy
            ]);

            // Build URL with query parameters (GET request - matching working format)
            $queryParams = http_build_query([
                'username' => env('SMS_USERNAME', 'Blessmore'),
                'password' => env('SMS_PASSWORD', 'Blessmore'),
                'msg' => $message_string,
                'shortcode' => env('SMS_SHORTCODE', '2343'),
                'sender_id' => env('SMS_SENDER_ID', 'StFrancis'),
                'phone' => '+' . $phone_number,
                'api_key' => env('SMS_API_KEY', '121231313213123123'),
            ]);

            $apiUrl = env('SMS_API_URL', 'https://www.cloudservicezm.com/smsservice/httpapi');

            // Send as GET request
            $sendSenderSMS = Http::withoutVerifying()
                ->timeout(15)
                ->get($apiUrl . '?' . $queryParams);

            // Log the response
            Log::info('SMS API Response', [
                'status' => $sendSenderSMS->status(),
                'body' => $sendSenderSMS->body(),
                'to' => substr($phone_number, 0, 6) . '****' . substr($phone_number, -3),
            ]);

            return $sendSenderSMS->successful() && (strtolower(trim($sendSenderSMS->body())) === 'success');
        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'error' => $e->getMessage(),
                'phone' => $phone_number,
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }
}
