<?php

namespace App\Console\Commands;

use App\Models\ParentGuardian;
use App\Models\Student;
use App\Support\PhoneNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Consolidates duplicate parent_guardian rows that collapse to the same
 * normalized phone — moves every child onto a single "keeper" row, promotes
 * the keeper's user_id when needed, then deletes the dupes. Default is
 * --dry-run; --commit performs the writes inside per-group transactions.
 */
class MergeDuplicateParents extends Command
{
    protected $signature = 'accounts:merge-duplicate-parents {--commit : Actually perform the merges (default is dry-run)}';

    protected $description = 'Merge parent_guardians that collapse to the same normalized phone (parents currently split across rows)';

    public function handle(): int
    {
        $commit = (bool) $this->option('commit');

        // 1. Build groups by normalized phone.
        $groups = [];
        ParentGuardian::with('students')->whereNotNull('phone')->orderBy('id')->chunk(500, function ($pgs) use (&$groups) {
            foreach ($pgs as $p) {
                $n = PhoneNormalizer::normalize($p->phone);
                if (! $n) continue;
                $groups[$n][] = $p;
            }
        });
        $dupes = array_filter($groups, fn ($g) => count($g) > 1);

        if (! $dupes) {
            $this->info('No duplicate parent_guardians on normalized phone — nothing to merge.');
            return self::SUCCESS;
        }

        $rows = []; $stats = ['groups' => 0, 'merged' => 0, 'failed' => 0, 'children_moved' => 0, 'rows_deleted' => 0];

        // 2. Per group: pick keeper, plan or commit the merge.
        foreach ($dupes as $phone => $pgs) {
            $stats['groups']++;
            try {
                $plan = $this->planMerge($pgs);

                if ($commit) {
                    DB::transaction(function () use ($plan) {
                        // 2a. Promote keeper's user_id if needed.
                        if (! $plan['keeper']->user_id && $plan['promoted_user_id']) {
                            DB::table('parent_guardians')->where('id', $plan['keeper']->id)
                                ->update(['user_id' => $plan['promoted_user_id']]);
                        }
                        // 2b. Reassign every dupe-child onto the keeper.
                        if ($plan['child_ids_to_move']) {
                            Student::whereIn('id', $plan['child_ids_to_move'])
                                ->update(['parent_guardian_id' => $plan['keeper']->id]);
                        }
                        // 2c. Delete the dupe parent_guardian rows.
                        ParentGuardian::whereIn('id', $plan['dupe_ids'])->delete();
                    });
                    $stats['merged']++;
                    $stats['children_moved'] += count($plan['child_ids_to_move']);
                    $stats['rows_deleted']   += count($plan['dupe_ids']);
                    $status = 'committed';
                } else {
                    $status = 'planned';
                }

                $rows[] = [
                    'phone'              => $phone,
                    'keeper_id'          => $plan['keeper']->id,
                    'keeper_name'        => $plan['keeper']->name,
                    'keeper_user_id'     => $plan['keeper']->user_id ?: $plan['promoted_user_id'] ?: '',
                    'kept_kids'          => $plan['keeper_child_count'],
                    'moved_kids'         => count($plan['child_ids_to_move']),
                    'deleted_pg_ids'     => implode(';', $plan['dupe_ids']),
                    'deleted_pg_names'   => implode(' | ', $plan['dupe_names']),
                    'orphaned_user_ids'  => implode(';', $plan['orphaned_user_ids']),
                    'status'             => $status,
                    'notes'              => $plan['notes'],
                ];
            } catch (\Throwable $e) {
                $stats['failed']++;
                Log::warning('Merge duplicate parents failed for ' . $phone . ': ' . $e->getMessage());
                $rows[] = [
                    'phone' => $phone, 'keeper_id' => '', 'keeper_name' => '', 'keeper_user_id' => '',
                    'kept_kids' => 0, 'moved_kids' => 0, 'deleted_pg_ids' => '', 'deleted_pg_names' => '',
                    'orphaned_user_ids' => '', 'status' => 'failed', 'notes' => substr($e->getMessage(), 0, 200),
                ];
            }
        }

        $csv = $this->writeCsv($rows, $commit);
        $this->info(($commit ? 'COMMIT' : 'DRY-RUN') . ' summary: ' . json_encode($stats));
        $this->info('Report: ' . $csv);

        return self::SUCCESS;
    }

    /**
     * Choose a keeper and compute the merge plan for one phone-group.
     * Keeper = row with most children (tie-break: lowest id).
     */
    private function planMerge(array $pgs): array
    {
        usort($pgs, function ($a, $b) {
            $byKids = $b->students->count() <=> $a->students->count();
            return $byKids !== 0 ? $byKids : ($a->id <=> $b->id);
        });
        $keeper = $pgs[0];
        $dupes  = array_slice($pgs, 1);

        $childIdsToMove = [];
        $dupeIds = []; $dupeNames = []; $orphanedUserIds = []; $notes = [];

        foreach ($dupes as $d) {
            $dupeIds[]   = $d->id;
            $dupeNames[] = $d->name;
            foreach ($d->students as $s) {
                $childIdsToMove[] = $s->id;
            }
            // If the dupe has a user_id and the keeper doesn't, we promote it.
            // If both have user_ids (different humans? or same), the dupe's becomes orphaned.
            if ($d->user_id && $d->user_id !== $keeper->user_id) {
                $orphanedUserIds[] = $d->user_id;
            }
        }

        // Pick a user_id for the keeper if it has none.
        $promotedUserId = null;
        if (! $keeper->user_id) {
            foreach ($dupes as $d) {
                if ($d->user_id) { $promotedUserId = $d->user_id; break; }
            }
            if ($promotedUserId) {
                // The user_id we just promoted is no longer "orphaned" — it's now on the keeper.
                $orphanedUserIds = array_values(array_filter($orphanedUserIds, fn ($u) => $u !== $promotedUserId));
                $notes[] = "promoted user_id={$promotedUserId} from dupe onto keeper";
            }
        }

        if ($orphanedUserIds) {
            $notes[] = 'dupe user_ids no longer linked (kept in users table): ' . implode(',', $orphanedUserIds);
        }

        return [
            'keeper'             => $keeper,
            'keeper_child_count' => $keeper->students->count(),
            'child_ids_to_move'  => $childIdsToMove,
            'dupe_ids'           => $dupeIds,
            'dupe_names'         => $dupeNames,
            'orphaned_user_ids'  => array_values(array_unique($orphanedUserIds)),
            'promoted_user_id'   => $promotedUserId,
            'notes'              => implode('; ', $notes),
        ];
    }

    private function writeCsv(array $rows, bool $commit): string
    {
        $name = ($commit ? 'committed' : 'dryrun') . '-' . now()->format('Ymd-His') . '.csv';
        $path = storage_path('app/parent-merge-' . $name);
        $fh = fopen($path, 'w');
        fputcsv($fh, ['phone','keeper_id','keeper_name','keeper_user_id','kept_kids','moved_kids','deleted_pg_ids','deleted_pg_names','orphaned_user_ids','status','notes']);
        foreach ($rows as $r) {
            fputcsv($fh, [
                $r['phone'], $r['keeper_id'], $r['keeper_name'], $r['keeper_user_id'],
                $r['kept_kids'], $r['moved_kids'], $r['deleted_pg_ids'], $r['deleted_pg_names'],
                $r['orphaned_user_ids'], $r['status'], $r['notes'],
            ]);
        }
        fclose($fh);
        return $path;
    }
}
