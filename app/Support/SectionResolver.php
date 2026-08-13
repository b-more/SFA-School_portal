<?php

namespace App\Support;

use App\Models\Grade;

/**
 * Single source of truth for the ECE / Primary / Secondary split.
 *
 * `GradingScale::determineGradeLevelFromGrade` collapses ECE into
 * primary (correct for grading scales, since ECE uses the primary
 * scale) but that's the wrong axis for pastoral / fee reporting where
 * ECE stands on its own. This helper carries that reporting split
 * without disturbing the grading path.
 */
class SectionResolver
{
    public const ECE       = 'ECE';
    public const PRIMARY   = 'Primary';
    public const SECONDARY = 'Secondary';

    public const ALL = [self::ECE, self::PRIMARY, self::SECONDARY];

    public static function sectionFor(Grade $grade): string
    {
        $name = $grade->name ?? '';

        if (in_array($name, ['Baby Class', 'Middle Class', 'Reception'], true)) {
            return self::ECE;
        }

        if (preg_match('/Form\s*\d+/i', $name)) {
            return self::SECONDARY;
        }

        if (preg_match('/Grade\s*(\d+)/i', $name, $m)) {
            return ((int) $m[1]) <= 7 ? self::PRIMARY : self::SECONDARY;
        }

        // Unknown naming — safest default is Primary; a Form/Grade 8+ pupil
        // that landed here has an exotic grade name that should be flagged.
        return self::PRIMARY;
    }
}
