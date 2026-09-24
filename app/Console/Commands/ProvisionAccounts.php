<?php

namespace App\Console\Commands;

use App\Constants\RoleConstants;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\User;
use App\Services\SmsService;
use App\Support\PhoneNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Idempotent bulk provisioning of parent + student user accounts.
 * - Looks up by normalized phone first → re-runs never duplicate.
 * - Replaces the legacy CreateStudentAccounts / SetupStudentParentAccounts in practice.
 * - Defaults to --dry-run; nothing is written unless --commit is passed.
 */
class ProvisionAccounts extends Command
{
    protected $signature = 'accounts:provision
        {--commit : Actually write users (default is dry-run)}
        {--send-sms : SMS each provisioned parent their login (no effect in dry-run)}
        {--role=parents : parents | students | all}';

    protected $description = 'Provision parent/student user accounts deterministically and (optionally) SMS credentials';

    private const SENTINEL_DOMAIN = '@no-email.stfrancisofassisizm.com';
    // Password alphabet without ambiguous characters (0/O/o/1/I/l/L).
    private const PW_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';

    public function handle(): int
    {
        $commit  = (bool) $this->option('commit');
        $sendSms = (bool) $this->option('send-sms') && $commit;
        $role    = strtolower($this->option('role') ?: 'parents');

        if (! in_array($role, ['parents', 'students', 'all'], true)) {
            $this->error("--role must be one of: parents, students, all");
            return self::INVALID;
        }

        $rows = [];

        if (in_array($role, ['parents', 'all'], true)) {
            $rows = array_merge($rows, $this->provisionParents($commit, $sendSms));
        }
        if (in_array($role, ['students', 'all'], true)) {
            $rows = array_merge($rows, $this->provisionStudents($commit));
        }

        $csv = $this->writeCsv($rows, $commit);

        $summary = $this->summarize($rows);
        $this->info(($commit ? 'COMMIT' : 'DRY-RUN') . ' summary: ' . json_encode($summary));
        $this->info('Report: ' . $csv);

        return self::SUCCESS;
    }

    // ── Parents ─────────────────────────────────────────────────────────────
    private function provisionParents(bool $commit, bool $sendSms): array
    {
        $rows = [];
        $parents = ParentGuardian::whereNull('user_id')->orderBy('id')->get();

        foreach ($parents as $p) {
            $phone = PhoneNormalizer::normalize($p->phone);
            $base = ['type' => 'parent', 'id' => $p->id, 'name' => $p->name, 'raw_phone' => $p->phone, 'phone' => $phone];

            if (! $phone) {
                $rows[] = $base + ['status' => 'skipped', 'notes' => 'no_recognisable_phone'];
                continue;
            }

            // 1. If a User already has this phone, just link the parent_guardian to it (no new login).
            $existing = User::where('phone', $phone)->first();
            if ($existing) {
                if ($commit) $p->update(['user_id' => $existing->id]);
                $rows[] = $base + [
                    'status'   => 'linked_existing_user',
                    'username' => $existing->username,
                    'email'    => $existing->email,
                    'password' => '(unchanged)',
                    'notes'    => "linked parent_guardian.user_id={$existing->id}",
                ];
                continue;
            }

            // 2. Otherwise create a fresh User.
            $username = PhoneNormalizer::digits($phone); // 260976xxxxxx — what they type at login
            $email    = "parent.{$username}" . self::SENTINEL_DOMAIN;
            $password = $this->randomPassword();

            if (! $commit) {
                $rows[] = $base + [
                    'status'   => 'planned',
                    'username' => $username,
                    'email'    => $email,
                    'password' => '(generated on commit)',
                    'notes'    => 'would create user + link',
                ];
                continue;
            }

            try {
                $user = DB::transaction(function () use ($p, $phone, $username, $email, $password) {
                    $u = User::create([
                        'name' => $p->name ?: 'Parent ' . $p->id,
                        'email' => $email,
                        'username' => $username,
                        'phone' => $phone,
                        'role_id' => RoleConstants::PARENT,
                        'password' => Hash::make($password),
                        'status' => 'active',
                        'must_change_password' => true,
                    ]);
                    $p->update(['user_id' => $u->id]);
                    return $u;
                });
            } catch (\Throwable $e) {
                $rows[] = $base + ['status' => 'failed', 'notes' => substr($e->getMessage(), 0, 200)];
                continue;
            }

            $rows[] = $base + [
                'status'   => 'created',
                'username' => $username,
                'email'    => $email,
                'password' => $password,
                'notes'    => "user_id={$user->id}",
            ];

            if ($sendSms) {
                $this->smsCredentialsToParent($p, $phone, $username, $password, $user->id);
            }
        }

        return $rows;
    }

    // ── Students ────────────────────────────────────────────────────────────
    private function provisionStudents(bool $commit): array
    {
        $rows = [];
        $students = Student::whereNull('user_id')->where('enrollment_status', 'active')->orderBy('id')->get();

        foreach ($students as $s) {
            $sid = $s->student_id_number ?: ('S' . $s->id);
            $base = ['type' => 'student', 'id' => $s->id, 'name' => $s->name, 'student_id_number' => $sid];

            $username = $sid;
            $email    = 'student.' . preg_replace('/[^A-Za-z0-9]/', '', $sid) . self::SENTINEL_DOMAIN;
            $password = $this->randomPassword();

            // Idempotent guard: same username already exists?
            if (User::where('username', $username)->exists() || User::where('email', $email)->exists()) {
                $rows[] = $base + ['status' => 'skipped', 'notes' => 'user_with_same_username_or_email_exists'];
                continue;
            }

            if (! $commit) {
                $rows[] = $base + [
                    'status'   => 'planned',
                    'username' => $username,
                    'email'    => $email,
                    'password' => '(generated on commit)',
                    'notes'    => 'would create user + link',
                ];
                continue;
            }

            try {
                $user = DB::transaction(function () use ($s, $email, $username, $password) {
                    $u = User::create([
                        'name' => $s->name ?: 'Student ' . $s->id,
                        'email' => $email,
                        'username' => $username,
                        'role_id' => RoleConstants::STUDENT,
                        'password' => Hash::make($password),
                        'status' => 'active',
                        'must_change_password' => true,
                    ]);
                    $s->update(['user_id' => $u->id]);
                    return $u;
                });
            } catch (\Throwable $e) {
                $rows[] = $base + ['status' => 'failed', 'notes' => substr($e->getMessage(), 0, 200)];
                continue;
            }

            $rows[] = $base + [
                'status'   => 'created',
                'username' => $username,
                'email'    => $email,
                'password' => $password,
                'notes'    => "user_id={$user->id}",
            ];
        }

        return $rows;
    }

    // ── Helpers ─────────────────────────────────────────────────────────────
    private function randomPassword(int $len = 8): string
    {
        $pw = '';
        $max = strlen(self::PW_ALPHABET) - 1;
        for ($i = 0; $i < $len; $i++) {
            $pw .= self::PW_ALPHABET[random_int(0, $max)];
        }
        return $pw;
    }

    private function smsCredentialsToParent(ParentGuardian $p, string $phone, string $username, string $password, int $userId): void
    {
        $children = $p->students()->where('enrollment_status', 'active')->pluck('name')->take(5)->all();
        $childList = $children ? ' Children: ' . implode(', ', $children) . '.' : '';
        $msg = "St Francis of Assisi: Your parent portal login. Phone: {$phone} Password: {$password}.{$childList} Please change password at first login.";

        try {
            app(SmsService::class)->send($msg, $phone, 'auth', $userId);
        } catch (\Throwable $e) {
            Log::warning('Provisioning SMS failed', ['phone' => $phone, 'error' => $e->getMessage()]);
        }
    }

    private function writeCsv(array $rows, bool $commit): string
    {
        $name = ($commit ? 'committed' : 'dryrun') . '-' . now()->format('Ymd-His') . '.csv';
        $path = storage_path('app/provisioning-' . $name);
        $fh = fopen($path, 'w');
        fputcsv($fh, ['type', 'id', 'name', 'student_id_number', 'phone', 'raw_phone', 'username', 'email', 'password', 'status', 'notes']);
        foreach ($rows as $r) {
            fputcsv($fh, [
                $r['type'] ?? '',
                $r['id'] ?? '',
                $r['name'] ?? '',
                $r['student_id_number'] ?? '',
                $r['phone'] ?? '',
                $r['raw_phone'] ?? '',
                $r['username'] ?? '',
                $r['email'] ?? '',
                $r['password'] ?? '',
                $r['status'] ?? '',
                $r['notes'] ?? '',
            ]);
        }
        fclose($fh);
        return $path;
    }

    private function summarize(array $rows): array
    {
        $sum = ['total' => count($rows)];
        foreach ($rows as $r) {
            $key = ($r['type'] ?? '?') . '_' . ($r['status'] ?? '?');
            $sum[$key] = ($sum[$key] ?? 0) + 1;
        }
        return $sum;
    }
}
