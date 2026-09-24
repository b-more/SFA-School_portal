<?php

namespace App\Console\Commands;

use App\Models\ParentGuardian;
use App\Support\PhoneNormalizer;
use Illuminate\Console\Command;

/**
 * Read-only audit: groups parent_guardian rows whose phones collapse to the
 * same E.164 number. Useful after the identity-data migration to size up the
 * manual merge queue before adding a UNIQUE phone index.
 */
class AccountsFindDuplicates extends Command
{
    protected $signature = 'accounts:find-dupes';

    protected $description = 'Report parent_guardians colliding on normalized phone (read-only)';

    public function handle(): int
    {
        $groups = [];

        ParentGuardian::with('students')->whereNotNull('phone')->orderBy('id')->chunk(500, function ($parents) use (&$groups) {
            foreach ($parents as $p) {
                $n = PhoneNormalizer::normalize($p->phone);
                if (! $n) continue;
                $groups[$n][] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'raw_phone' => $p->phone,
                    'email' => $p->email,
                    'user_id' => $p->user_id,
                    'children' => $p->students->pluck('name')->all(),
                ];
            }
        });

        $dupes = array_filter($groups, fn ($g) => count($g) > 1);
        if (! $dupes) {
            $this->info('No duplicate parent_guardians on normalized phone — safe to add UNIQUE index.');
            return self::SUCCESS;
        }

        $path = storage_path('app/parent-duplicates-' . now()->format('Ymd-His') . '.csv');
        $fh = fopen($path, 'w');
        fputcsv($fh, ['normalized_phone', 'parent_id', 'name', 'raw_phone', 'email', 'user_id', 'children']);
        foreach ($dupes as $phone => $rows) {
            foreach ($rows as $r) {
                fputcsv($fh, [
                    $phone,
                    $r['id'],
                    $r['name'],
                    $r['raw_phone'],
                    $r['email'],
                    $r['user_id'] ?? '',
                    implode('; ', $r['children']),
                ]);
            }
        }
        fclose($fh);

        $this->warn(count($dupes) . " duplicate phone group(s) found across " .
            array_sum(array_map('count', $dupes)) . " parent_guardian rows.");
        $this->info('Report: ' . $path);

        return self::SUCCESS;
    }
}
