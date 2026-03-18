<?php

namespace App\Console\Commands;

use App\Models\Createur;
use App\Models\CreateurStatMensuelle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetStatsMensuellesCreateurs extends Command
{
    protected $signature = 'createurs:reset-stats-mois
                            {--dry-run : Afficher les actions sans modifier la base}';

    protected $description = 'Le 1er du mois : archive le mois précédent puis remet à zéro jours/heures/diamants de tous les créateurs.';

    public function handle(): int
    {
        $today = Carbon::today();
        $isFirstOfMonth = $today->day === 1;

        if (! $isFirstOfMonth) {
            if ($this->input->isInteractive()) {
                $this->warn('Cette commande est conçue pour être exécutée le 1er du mois. Aujourd\'hui = ' . $today->format('d/m/Y') . '.');
                if (! $this->confirm('Voulez-vous quand même exécuter la remise à zéro (mois précédent = ' . $today->copy()->subMonth()->format('F Y') . ') ?', false)) {
                    return self::SUCCESS;
                }
            } else {
                $this->info('Exécution ignorée (pas le 1er du mois, lancement automatique).');
                return self::SUCCESS;
            }
        }

        $previous = $today->copy()->subMonth();
        $annee = (int) $previous->format('Y');
        $mois = (int) $previous->format('n');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('[DRY-RUN] Aucune modification en base.');
        }

        $this->info("Mois à archiver : {$previous->translatedFormat('F Y')} (année={$annee}, mois={$mois}).");
        $createurs = Createur::all();
        $archived = 0;
        $reset = 0;

        foreach ($createurs as $createur) {
            $hasData = $createur->heures_mois !== null
                || $createur->jours_mois !== null
                || $createur->diamants !== null;
            $hasNonZero = (int) ($createur->heures_mois ?? 0) !== 0
                || (int) ($createur->jours_mois ?? 0) !== 0
                || (int) ($createur->diamants ?? 0) !== 0;

            if ($hasData && $hasNonZero) {
                if (! $dryRun) {
                    CreateurStatMensuelle::updateOrCreate(
                        [
                            'createur_id' => $createur->id,
                            'annee' => $annee,
                            'mois' => $mois,
                        ],
                        [
                            'jours_stream' => $createur->jours_mois ?? 0,
                            'heures_stream' => $createur->heures_mois ?? 0,
                            'diamants' => $createur->diamants ?? 0,
                        ]
                    );
                }
                $archived++;
                $this->line("  Archive : {$createur->nom} — J={$createur->jours_mois} H={$createur->heures_mois} D={$createur->diamants}");
            }

            if (! $dryRun) {
                $createur->update([
                    'heures_mois' => 0,
                    'jours_mois' => 0,
                    'diamants' => 0,
                ]);
            }
            $reset++;
        }

        $this->info(sprintf(
            'Terminé. %d créateur(s) avec données archivées pour %s, %d créateur(s) remis à zéro (jours/heures/diamants).',
            $archived,
            $previous->translatedFormat('F Y'),
            $reset
        ));

        return self::SUCCESS;
    }
}
