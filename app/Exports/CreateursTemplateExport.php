<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

/**
 * Modèle Excel pour l'import créateurs.
 * Structure alignée sur « Données du/de la créateur(trice) » :
 * Colonne C = Nom d'utilisateur, H = Diamants, I = Durée de LIVE (heures), J = Jours.
 */
class CreateursTemplateExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'Période des données',                           // A
            'ID créateur(trice)',                            // B
            'Nom d\'utilisateur du/de la créateur(trice)',   // C — utilisé pour l'import
            'Groupe',                                        // D
            'Agent',                                         // E
            'Date d\'établissement de la relation',          // F
            'Jours depuis l\'adhésion',                      // G
            'Diamants',                                      // H — utilisé pour l'import
            'Durée de LIVE',                                 // I — utilisé pour l'import (ex. 7h30 ou 7.5)
            'Jours de passage en LIVE valides',              // J — utilisé pour l'import
            'Nouveaux followers',                            // K
            'Diffusions LIVE',                               // L
            'Diamants le mois dernier',                       // M
            'Durée de LIVE (en heures) le mois dernier',      // N
            'Jours de passage en LIVE valides le mois dernier', // O
        ];
    }

    public function array(): array
    {
        return [
            [
                '',                    // A
                '',                    // B
                'exemple_username',    // C — à remplir : username exact du créateur
                '',                    // D
                '',                    // E
                '',                    // F
                '',                    // G
                0,                     // H — Diamants
                '7h30',                // I — Durée de LIVE (format 7h30 ou 7.5)
                7,                     // J — Jours de passage en LIVE valides (entier)
                '',                    // K
                '',                    // L
                '',                    // M
                '',                    // N
                '',                    // O
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 18,
            'C' => 38,
            'D' => 12,
            'E' => 12,
            'F' => 28,
            'G' => 22,
            'H' => 12,
            'I' => 16,
            'J' => 32,
            'K' => 18,
            'L' => 18,
            'M' => 22,
            'N' => 42,
            'O' => 45,
        ];
    }
}
