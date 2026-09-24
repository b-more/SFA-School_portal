<?php

namespace App\Support;

/**
 * Canonicalises Zambian mobile numbers to E.164 (+260XXXXXXXXX).
 * Returns null when the input cannot be confidently mapped to a 9-digit
 * Zambian subscriber number — caller must handle "could not normalize"
 * (e.g. log + leave unchanged) rather than silently corrupting data.
 */
class PhoneNormalizer
{
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        // Strip everything that isn't a digit or leading +.
        $digits = preg_replace('/[^\d]/', '', $phone);
        if ($digits === '' || $digits === null) {
            return null;
        }

        // Trim leading zeros from country-code-style inputs (e.g. 00260…).
        if (str_starts_with($digits, '00')) {
            $digits = ltrim($digits, '0');
        }

        // Zambian numbers are 9 digits after the country code (260).
        // Accept: 260976xxxxxx (12), 0976xxxxxx (10), 976xxxxxx (9).
        if (strlen($digits) === 12 && str_starts_with($digits, '260')) {
            $subscriber = substr($digits, 3);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $subscriber = substr($digits, 1);
        } elseif (strlen($digits) === 9) {
            $subscriber = $digits;
        } else {
            return null;
        }

        // Zambian mobile prefixes start with 7 or 9 (MTN/Airtel/Zamtel).
        // Landlines (21x…) are rare for parents but we'll allow them through
        // rather than reject — only enforce the 9-digit length.
        if (! ctype_digit($subscriber) || strlen($subscriber) !== 9) {
            return null;
        }

        return '+260' . $subscriber;
    }

    /**
     * Convenience: the digits-only (no `+`) form, e.g. "260976123456".
     * Useful as a username / sentinel-email suffix.
     */
    public static function digits(?string $phone): ?string
    {
        $n = self::normalize($phone);
        return $n ? ltrim($n, '+') : null;
    }
}
