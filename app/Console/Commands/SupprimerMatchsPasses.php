<?php

namespace App\Console\Commands;

use App\Http\Controllers\MatchController;
use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SupprimerMatchsPasses extends Command
{
    protected $signature = 'matchs:supprimer-passes';

    protected $description = 'Supprime les matchs passés (date < aujourd\'hui, ou aujourd\'hui fini depuis +1h).';

    public function handle(): int
    {
        $types = array_keys(MatchController::MATCH_TYPES);
        $aujourdhui = Carbon::now()->startOfDay()->format('Y-m-d');
        $seuil = Carbon::now()->subHour();
        $seuilStr = $seuil->format('Y-m-d H:i:s');
        $driver = Planning::getConnection()->getDriverName();

        $baseQuery = fn () => Planning::query()->whereIn('type', $types);

        $deleted = 0;

        // 1) Matchs dont la date est strictement avant aujourd'hui
        $deleted += $baseQuery()->where('date', '<', $aujourdhui)->delete();

        // 2) Matchs du jour dont date+heure est passée depuis plus d'1h
        if ($driver === 'sqlite') {
            $deleted += $baseQuery()
                ->where('date', $aujourdhui)
                ->whereRaw(
                    '(date || " " || COALESCE(heure, "23:59:59")) < ?',
                    [$seuilStr]
                )
                ->delete();
        } else {
            $deleted += $baseQuery()
                ->where('date', $aujourdhui)
                ->whereRaw(
                    'CAST(CONCAT(planning.date, " ", COALESCE(planning.heure, "23:59:59")) AS DATETIME) < ?',
                    [$seuilStr]
                )
                ->delete();
        }

        if ($deleted > 0) {
            $this->info("Matchs passés supprimés : {$deleted}.");
        }

        return self::SUCCESS;
    }

    /** Appelable depuis le contrôleur pour nettoyer à l’affichage si le cron ne tourne pas. */
    public static function nettoyerMatchsPasses(): int
    {
        $types = array_keys(MatchController::MATCH_TYPES);
        $aujourdhui = Carbon::now()->startOfDay()->format('Y-m-d');
        return Planning::query()->whereIn('type', $types)->where('date', '<', $aujourdhui)->delete();
    }
}
