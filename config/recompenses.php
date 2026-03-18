<?php

return [
    /*
    | Budget total pour les récompenses (en €). Le montant affiché diminue
    | à chaque récompense attribuée. Définir dans .env : RECOMPENSES_BUDGET_TOTAL=15
    */
    'budget_total' => (float) env('RECOMPENSES_BUDGET_TOTAL', 15),

    /*
    | Délai en secondes avant que la facture soit téléchargeable après le choix du mode
    | de réception (pour limiter la charge serveur).
    */
    'facture_delai_secondes' => (int) env('RECOMPENSES_FACTURE_DELAI_SECONDES', 5),

    /*
    | Infos société pour la facture PDF
    | Logo : chemin absolu vers l'image (PNG/JPG) ou null pour utiliser public/images/logo-unions-agency.png
    */
    'facture' => [
        'societe' => env('FACTURE_SOCIETE', 'Unions Agency'),
        'forme' => env('FACTURE_FORME', 'Société SARL'),
        'adresse' => env('FACTURE_ADRESSE', ''),
        'snapchat' => env('FACTURE_SNAPCHAT', 'unionsagency'),
        'site' => env('FACTURE_SITE', 'unionsagency.com'),
        'logo' => env('FACTURE_LOGO'), // chemin absolu optionnel, sinon public/images/logo-unions-agency.png
    ],
];
