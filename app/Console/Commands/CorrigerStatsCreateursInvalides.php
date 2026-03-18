<?php

namespace App\Console\Commands;

use App\Models\Createur;
use Illuminate\Console\Command;

class CorrigerStatsCreateursInvalides extends Command
{
    protected $signature = 'createurs:corriger-stats-invalides
                            {--dry-run : Afficher les corrections sans modifier la base}
                            {--nom= : Limiter à un créateur (nom ou pseudo contenant cette chaîne)}';

    protected $description = 'Corrige les stats invalides (jours > 31, heures > 744) : plafonne à 31 jours et 744 heures.';

    private const JOURS_MAX = 31;
    private const HEURES_MAX = 744;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $nomFilter = $this->option('nom');

        $query = Createur::query();
        if ($nomFilter !== null && $nomFilter !== '') {
            $query->where(function ($q) use ($nomFilter) {
                $q->where('nom', 'like', '%' . $nomFilter . '%')
                    ->orWhere('pseudo_tiktok', 'like', '%' . $nomFilter . '%')
                    ->orWhere('email', 'like', '%' . $nomFilter . '%');
            });
        }

        $createurs = $query->get();
        $corrected = 0;

        foreach ($createurs as $createur) {
            $jours = $createur->jours_mois;
            $heures = $createur->heures_mois;
            $diamants = $createur->diamants;

            $needJours = $jours !== null && ((int) $jours > self::JOURS_MAX || (int) $jours < 0);
            $needHeures = $heures !== null && ((float) $heures > self::HEURES_MAX || (float) $heures < 0);
            $needDiamants = $diamants !== null && (int) $diamants < 0;

            if (! $needJours && ! $needHeures && ! $needDiamants) {
                continue;
            }

            $newJours = $jours !== null ? max(0, min(self::JOURS_MAX, (int) $jours)) : null;
            $newHeures = $heures !== null ? round(max(0, min(self::HEURES_MAX, (float) $heures)), 2) : null;
            $newDiamants = $diamants !== null ? max(0, (int) $diamants) : null;

            $this->line(sprintf(
                '  %s : Jours %s → %s, Heures %s → %s, Diamants %s → %s',
                $createur->nom,
                $jours ?? '—',
                $newJours ?? '—',
                $heures ?? '—',
                $newHeures ?? '—',
                $diamants ?? '—',
                $newDiamants ?? '—'
            ));

            if (! $dryRun) {
                $createur->update([
                    'jours_mois' => $newJours,
                    'heures_mois' => $newHeures,
                    'diamants' => $newDiamants,
                ]);
            }
            $corrected++;
        }

        if ($corrected === 0) {
            $this->info('Aucune stat invalide trouvée.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("[DRY-RUN] {$corrected} créateur(s) auraient été corrigés. Relancez sans --dry-run pour appliquer.");
        } else {
            $this->info("{$corrected} créateur(s) corrigé(s).");
        }

        return self::SUCCESS;
    }
}
