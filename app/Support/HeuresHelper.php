<?php

namespace App\Support;

/**
 * Heures avec minutes : format "7h30", "7h59", etc.
 * Stockage en décimal (7.5 = 7h30, 7.983... = 7h59).
 */
class HeuresHelper
{
    /**
     * Affiche un nombre décimal d'heures au format "7h30" ou "7h00".
     */
    public static function format(?float $decimalHours): string
    {
        if ($decimalHours === null || $decimalHours < 0) {
            return '—';
        }
        $h = (int) floor($decimalHours);
        $minutes = (int) round(($decimalHours - $h) * 60);
        if ($minutes >= 60) {
            $h += 1;
            $minutes = 0;
        }
        return $h . 'h' . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Parse une saisie "7h30", "7:30", "1h 14min 14s", "7.5", "7,5" ou "7" en décimal.
     * Affichage : décimal → "1h14" (heures + minutes, pas de secondes).
     */
    public static function parse(?string $input): ?float
    {
        if ($input === null || trim($input) === '') {
            return null;
        }
        $input = trim(str_replace(',', '.', $input));
        // 1h 14min 14s ou 1h14min14s → 1h14 (secondes incluses dans le décimal, affichage arrondi en minutes)
        if (preg_match('/^(\d+)\s*h\s*(\d+)\s*min\s*(\d+)\s*s/i', $input, $m)) {
            $h = (int) $m[1];
            $min = min(59, (int) $m[2]);
            $sec = min(59, (int) $m[3]);
            return $h + $min / 60 + $sec / 3600;
        }
        if (preg_match('/^(\d+)\s*h\s*(\d+)$/i', $input, $m)) {
            $h = (int) $m[1];
            $min = (int) $m[2];
            if ($min >= 60) {
                $min = 59;
            }
            return $h + $min / 60;
        }
        // Format 7:30 (heures:minutes)
        if (preg_match('/^(\d+):(\d+)$/', $input, $m)) {
            $h = (int) $m[1];
            $min = (int) $m[2];
            if ($min >= 60) {
                $min = 59;
            }
            return $h + $min / 60;
        }
        if (is_numeric($input)) {
            $v = (float) $input;
            return $v >= 0 ? $v : null;
        }
        return null;
    }
}
