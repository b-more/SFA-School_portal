<?php

use App\Support\PhoneNormalizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Identity-data cleanup: normalize phones to +260E.164, replace auto-generated
 * dummy emails with a single sentinel pattern, add a must_change_password flag.
 * Idempotent and conservative — never overwrites real emails or merges rows.
 */
return new class extends Migration
{
    private const STUDENT_ROLE = 3;
    private const PARENT_ROLE  = 4;
    private const SENTINEL_DOMAIN = '@no-email.stfrancisofassisizm.com';

    public function up(): void
    {
        // 1. Schema changes ---------------------------------------------------
        if (! Schema::hasColumn('users', 'must_change_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('must_change_password')->default(false)->after('status');
            });
        }
        // Non-unique index — uniqueness deferred until dupes are merged.
        try {
            Schema::table('parent_guardians', function (Blueprint $table) {
                $table->index('phone', 'parent_guardians_phone_index');
            });
        } catch (\Throwable $e) {
            // Index probably already exists from a previous attempt — fine.
        }

        // 2. Phone normalization ---------------------------------------------
        $this->normalizePhones('users');
        $this->normalizePhones('parent_guardians');

        // 3. Sentinel emails for student / parent users ----------------------
        $this->fillSentinelEmails();
    }

    public function down(): void
    {
        // Intentionally non-reversible — restoring a stew of variant phone
        // formats and autogen emails would be misleading. Re-run the migration
        // with --pretend to inspect; rolling back the schema additions only:
        if (Schema::hasColumn('users', 'must_change_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('must_change_password');
            });
        }
        try {
            Schema::table('parent_guardians', function (Blueprint $table) {
                $table->dropIndex('parent_guardians_phone_index');
            });
        } catch (\Throwable $e) {
            // ignore
        }
    }

    private function normalizePhones(string $table): void
    {
        $rows = DB::table($table)->select('id', 'phone')->whereNotNull('phone')->get();
        $normalized = [];   // id => normalized phone
        $unrecognised = []; // id => original phone

        foreach ($rows as $row) {
            $n = PhoneNormalizer::normalize($row->phone);
            if ($n === null) {
                $unrecognised[$row->id] = $row->phone;
                continue;
            }
            $normalized[$row->id] = $n;
        }

        // Detect collisions (two ids mapping to same normalized phone) and skip them.
        $counts = array_count_values($normalized);
        $skipped = [];
        foreach ($normalized as $id => $n) {
            if ($counts[$n] > 1) {
                $skipped[$id] = $n;
            }
        }

        $changed = 0;
        foreach ($normalized as $id => $n) {
            if (isset($skipped[$id])) continue;
            $current = DB::table($table)->where('id', $id)->value('phone');
            if ($current === $n) continue; // already normalized
            DB::table($table)->where('id', $id)->update(['phone' => $n]);
            $changed++;
        }

        Log::info("Phone normalization on `{$table}`", [
            'total_rows_with_phone' => $rows->count(),
            'changed' => $changed,
            'already_normalized' => count($normalized) - $changed - count($skipped),
            'collisions_skipped' => count($skipped),
            'unrecognised' => count($unrecognised),
        ]);
        if ($skipped) {
            Log::warning("Phone normalization collisions on `{$table}` — admin must merge", [
                'sample' => array_slice($skipped, 0, 10, true),
            ]);
        }
    }

    private function fillSentinelEmails(): void
    {
        // STUDENTS — local-part from students.student_id_number when available.
        DB::table('users as u')
            ->leftJoin('students as s', 's.user_id', '=', 'u.id')
            ->where('u.role_id', self::STUDENT_ROLE)
            ->where(function ($q) {
                $q->whereNull('u.email')
                  ->orWhere('u.email', 'like', '%@student.com')
                  ->orWhere('u.email', 'like', '%@parent.com')
                  ->orWhere('u.email', 'like', '%@stfrancisofassisizm.com');
            })
            ->select('u.id as uid', 'u.email as email', 's.student_id_number as sid')
            ->orderBy('u.id')
            ->chunk(200, function ($users) {
                foreach ($users as $u) {
                    $local = $u->sid ? 'student.' . preg_replace('/[^A-Za-z0-9]/', '', $u->sid) : 'student.uid' . $u->uid;
                    $this->applySentinel('users', $u->uid, $local);
                }
            });

        // PARENTS — local-part from normalized phone or NRC fallback.
        DB::table('users as u')
            ->leftJoin('parent_guardians as p', 'p.user_id', '=', 'u.id')
            ->where('u.role_id', self::PARENT_ROLE)
            ->where(function ($q) {
                $q->whereNull('u.email')
                  ->orWhere('u.email', 'like', '%@parent.com')
                  ->orWhere('u.email', 'like', '%@student.com')
                  ->orWhere('u.email', 'like', '%@stfrancisofassisizm.com');
            })
            ->select('u.id as uid', 'u.email as email', 'u.phone as uphone', 'p.phone as pphone', 'p.nrc as nrc')
            ->orderBy('u.id')
            ->chunk(200, function ($users) {
                foreach ($users as $u) {
                    $digits = PhoneNormalizer::digits($u->uphone) ?? PhoneNormalizer::digits($u->pphone);
                    if ($digits) {
                        $local = 'parent.' . $digits;
                    } elseif (! empty($u->nrc)) {
                        $local = 'parent.nrc.' . preg_replace('/[^A-Za-z0-9]/', '', $u->nrc);
                    } else {
                        $local = 'parent.uid' . $u->uid;
                    }
                    $this->applySentinel('users', $u->uid, $local);
                }
            });
    }

    /**
     * Set users.email to {local}@no-email.stfrancisofassisizm.com unless that
     * value would collide with another row (unique index) — in which case we
     * tack on -{uid} to keep it distinct, then last-resort skip + log.
     */
    private function applySentinel(string $table, int $uid, string $local): void
    {
        $sentinel = strtolower($local) . self::SENTINEL_DOMAIN;
        $current = DB::table($table)->where('id', $uid)->value('email');
        if ($current === $sentinel) return; // already there

        $existing = DB::table($table)->where('email', $sentinel)->where('id', '!=', $uid)->value('id');
        if ($existing) {
            $sentinel = strtolower($local) . '-uid' . $uid . self::SENTINEL_DOMAIN;
            $existing = DB::table($table)->where('email', $sentinel)->where('id', '!=', $uid)->value('id');
            if ($existing) {
                Log::warning('Sentinel email collision — skipped', ['user_id' => $uid, 'candidate' => $sentinel]);
                return;
            }
        }
        DB::table($table)->where('id', $uid)->update(['email' => $sentinel]);
    }
};
