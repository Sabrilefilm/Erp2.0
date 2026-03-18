<?php

namespace App\Exports;

use App\Models\Createur;
use App\Support\HeuresHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Export des créateurs avec Heures, Jours et Diamants (données mois en cours).
 */
class CreateursDonneesExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected \Illuminate\Support\Collection $createurs
    ) {}

    public function headings(): array
    {
        return [
            'Nom',
            'Nom d\'utilisateur',
            'Agence / Équipe',
            'Durée de LIVE (heures)',
            'Jours de passage en LIVE valides',
            'Diamants',
        ];
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return $this->createurs->map(function (Createur $c) {
            $heures = $c->heures_mois !== null ? HeuresHelper::format((float) $c->heures_mois) : '0h00';
            $jours = $c->jours_mois !== null ? (int) $c->jours_mois : 0;
            $diamants = $c->diamants !== null ? (int) $c->diamants : 0;

            return [
                $c->nom ?: $c->user?->name ?? '—',
                $c->user?->username ?? '—',
                $c->equipe?->nom ?? '—',
                $heures,
                $jours,
                $diamants,
            ];
        });
    }
}
