<?php

use App\Support\PhoneNormalizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Copy the parent_guardian's normalized phone onto its linked parent User
 * when User.phone is NULL — so phone login and SMS reset can find them.
 * Skips when the normalized phone is already in use by another User
 * (manual merge needed in that case).
 */
return new class extends Migration
{
    private const PARENT_ROLE = 4;

    public function up(): void
    {
        $copied = 0; $skippedCollision = 0; $skippedUnrec = 0;

        $pairs = DB::table('users as u')
            ->join('parent_guardians as p', 'p.user_id', '=', 'u.id')
            ->where('u.role_id', self::PARENT_ROLE)
            ->whereNull('u.phone')
            ->whereNotNull('p.phone')
            ->select('u.id as uid', 'p.phone as pphone')
            ->get();

        foreach ($pairs as $row) {
            $n = PhoneNormalizer::normalize($row->pphone);
            if (! $n) { $skippedUnrec++; continue; }
            $clash = DB::table('users')->where('phone', $n)->where('id', '!=', $row->uid)->exists();
            if ($clash) { $skippedCollision++; continue; }
            DB::table('users')->where('id', $row->uid)->update(['phone' => $n]);
            $copied++;
        }

        Log::info('Backfill user phones from parents', compact('copied', 'skippedCollision', 'skippedUnrec'));
    }

    public function down(): void
    {
        // Non-reversible — we don't know which user phones were originally NULL.
    }
};
