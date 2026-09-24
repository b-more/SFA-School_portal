<?php

namespace App\Console\Commands;

use App\Constants\RoleConstants;
use App\Models\ParentGuardian;
use App\Models\User;
use App\Support\PhoneNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Bulk-reset every parent's login password to a shared default and force them
 * to change it on first login.
 *
 * Behaviour for each parent_guardian row with a phone:
 *   - normalises the phone to E.164 (+260…),
 *   - ensures the linked user has role_id=PARENT, status=active, phone set,
 *   - writes the new hashed password,
 *   - sets must_change_password=true so the parent app pushes them straight
 *     to the change-password screen on first login.
 *
 * Defaults to --dry-run. Pass --commit to actually write.
 */
class ResetParentsPassword extends Command
{
    protected $signature = 'accounts:reset-parents-password
        {--password=Parent1234 : Shared default password to set for all parents}
        {--commit : Write changes (otherwise dry-run only)}
        {--limit= : Optional row limit (useful for staged rollouts)}
        {--only-without-login : Only touch users who have never logged in}';

    protected $description = 'Bulk-reset every parent\'s password to a shared default and force a change on first login.';

    public function handle(): int
    {
        $password = (string) $this->option('password');
        if (strlen($password) < 6) {
            $this->error('Password must be at least 6 characters.');
            return self::FAILURE;
        }
        $commit = (bool) $this->option('commit');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $onlyWithoutLogin = (bool) $this->option('only-without-login');

        $hash = Hash::make($password);
        $counts = [
            'matched' => 0,
            'updated' => 0,
            'role_fixed' => 0,
            'phone_set_on_user' => 0,
            'skipped_no_phone' => 0,
            'skipped_no_user' => 0,
            'skipped_already_logged_in' => 0,
            'failed' => 0,
        ];

        $query = ParentGuardian::query()
            ->whereNotNull('parent_guardians.user_id')
            ->whereNotNull('parent_guardians.phone')
            ->where('parent_guardians.phone', '!=', '');

        if ($limit) {
            $query->limit($limit);
        }

        $parents = $query->get();
        $counts['matched'] = $parents->count();

        $this->info(($commit ? 'COMMIT' : 'DRY-RUN') . ': processing ' . $counts['matched'] . ' parent rows.');
        $bar = $this->output->createProgressBar($counts['matched']);
        $bar->start();

        foreach ($parents as $p) {
            $phone = PhoneNormalizer::normalize($p->phone);
            if (! $phone) {
                $counts['skipped_no_phone']++;
                $bar->advance();
                continue;
            }
            $user = User::find($p->user_id);
            if (! $user) {
                $counts['skipped_no_user']++;
                $bar->advance();
                continue;
            }
            if ($onlyWithoutLogin && $user->last_login) {
                $counts['skipped_already_logged_in']++;
                $bar->advance();
                continue;
            }

            $updates = [
                'password' => $hash,
                'must_change_password' => true,
                'status' => 'active',
            ];
            if ((int) $user->role_id !== RoleConstants::PARENT) {
                $updates['role_id'] = RoleConstants::PARENT;
                $counts['role_fixed']++;
            }
            if ($user->phone !== $phone) {
                $updates['phone'] = $phone;
                $counts['phone_set_on_user']++;
            }

            if ($commit) {
                try {
                    DB::transaction(function () use ($user, $updates) {
                        $user->update($updates);
                        // Invalidate all existing Sanctum tokens so old sessions can't be used.
                        $user->tokens()->delete();
                    });
                    $counts['updated']++;
                } catch (\Throwable $e) {
                    // Retry without the phone update — common when a duplicate user record
                    // exists; the user that already owns the phone has been reset; we still
                    // want the duplicate's password + role updated so any email/username
                    // login they may use also gets the new default.
                    if (str_contains($e->getMessage(), 'users_phone_unique') && isset($updates['phone'])) {
                        $retry = $updates;
                        unset($retry['phone']);
                        $retry['updated_at'] = now();
                        try {
                            DB::transaction(function () use ($user, $retry) {
                                // Use the query builder so the failed model state doesn't leak phone= back into the UPDATE.
                                DB::table('users')->where('id', $user->id)->update($retry);
                                $user->tokens()->delete();
                            });
                            $counts['updated']++;
                            $counts['phone_collision_skipped'] = ($counts['phone_collision_skipped'] ?? 0) + 1;
                        } catch (\Throwable $e2) {
                            $counts['failed']++;
                            $this->newLine();
                            $this->warn(sprintf('user #%d retry failed: %s', $user->id, $e2->getMessage()));
                        }
                    } else {
                        $counts['failed']++;
                        $this->newLine();
                        $this->warn(sprintf('user #%d failed: %s', $user->id, $e->getMessage()));
                    }
                }
            } else {
                $counts['updated']++; // would-update
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info(sprintf('%s — %s', $commit ? 'COMMITTED' : 'DRY-RUN', $commit ? 'changes are live.' : 'no changes written.'));
        foreach ($counts as $k => $v) {
            $this->line(sprintf('  %-30s %d', $k, $v));
        }
        $this->line('');
        $this->line('Default password: ' . $password);
        $this->line('Login: phone (any common format, e.g. 097…) → password → forced change screen.');
        if (! $commit) {
            $this->warn('Re-run with --commit to apply.');
        }

        return self::SUCCESS;
    }
}
