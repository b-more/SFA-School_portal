<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Constants\RoleConstants;
use App\Filament\Resources\DriverResource;
use App\Models\BusFareStructure;
use App\Models\User;
use App\Models\UserCredential;
use App\Services\SmsService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateDriver extends CreateRecord
{
    protected static string $resource = DriverResource::class;

    protected ?array $assignedRouteIds = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->assignedRouteIds = array_filter((array) ($data['route_ids'] ?? []));
        unset($data['route_ids']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $password = Str::password(10, symbols: false);

            $email = $data['email'] ?: $this->generateEmail($data['name']);

            $user = User::create([
                'role_id' => RoleConstants::DRIVER,
                'name' => $data['name'],
                'email' => $email,
                'phone' => $data['phone'] ?? null,
                'username' => $data['username'] ?? null,
                'password' => Hash::make($password),
                'status' => $data['status'] ?? 'active',
                'must_change_password' => true,
                'notes' => $data['notes'] ?? null,
            ]);

            if (! empty($this->assignedRouteIds)) {
                BusFareStructure::whereIn('id', $this->assignedRouteIds)
                    ->whereNull('driver_user_id')
                    ->update(['driver_user_id' => $user->id]);
            }

            UserCredential::create([
                'user_id' => $user->id,
                'username' => $email,
                'password' => $password,
                'is_sent' => false,
                'delivery_method' => 'manual',
            ]);

            $smsSent = false;
            if (! empty($data['phone'])) {
                try {
                    $message = "Welcome to St. Francis of Assisi Portal!\n\n".
                        "Your driver account has been created.\n\n".
                        "Email: {$email}\n".
                        "Password: {$password}\n\n".
                        'Login at: '.config('app.url')."/admin\n\n".
                        'You will be asked to change this password on first login.';

                    $smsSent = app(SmsService::class)->send(
                        $message,
                        $data['phone'],
                        'driver_credentials',
                        $user->id
                    );
                } catch (\Throwable $e) {
                    Log::error('Driver SMS failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
            }

            if ($smsSent) {
                UserCredential::where('user_id', $user->id)->update([
                    'is_sent' => true,
                    'sent_at' => now(),
                    'delivery_method' => 'sms',
                ]);
            }

            $body = "Email: {$email} | Password: {$password}";
            $body .= $smsSent ? ' (SMS sent to driver)' : ' (please share these with the driver)';

            Notification::make()
                ->title('Driver account created')
                ->body($body)
                ->success()
                ->persistent()
                ->send();

            return $user;
        });
    }

    protected function generateEmail(string $name): string
    {
        $slug = Str::of($name)->lower()->replaceMatches('/[^a-z0-9]+/', '.')->trim('.');
        $base = $slug ?: 'driver';
        $email = "{$base}@stfrancisofassisizm.com";
        $i = 1;
        while (User::where('email', $email)->exists()) {
            $email = "{$base}{$i}@stfrancisofassisizm.com";
            $i++;
        }

        return $email;
    }
}
