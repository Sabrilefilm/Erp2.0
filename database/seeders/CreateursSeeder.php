<?php

namespace Database\Seeders;

use App\Models\Createur;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class CreateursSeeder extends Seeder
{
    /**
     * Ajoute des créateurs de démo si la table est vide.
     * À lancer seul : php artisan db:seed --class=CreateursSeeder
     */
    public function run(): void
    {
        if (Createur::count() > 0) {
            return;
        }

        $equipe = Equipe::first();
        $agent = User::where('role', User::ROLE_AGENT)->first();
        $ambassadeur = User::where('role', User::ROLE_AMBASSADEUR)->first();

        Createur::create([
            'nom' => 'Inès Saidi',
            'email' => 'ines.saidi@outlook.com',
            'pseudo_tiktok' => '@inessaidi',
            'statut' => 'Actif',
            'equipe_id' => $equipe?->id,
            'agent_id' => $agent?->id,
            'ambassadeur_id' => $ambassadeur?->id,
            'stats_vues' => 125000,
            'stats_followers' => 45000,
        ]);
        Createur::create([
            'nom' => 'Kylian Petit',
            'email' => 'kylian.petit@gmail.com',
            'pseudo_tiktok' => '@kylian.pt',
            'statut' => 'Actif',
            'equipe_id' => $equipe?->id,
            'agent_id' => $agent?->id,
            'stats_vues' => 89000,
            'stats_followers' => 32000,
        ]);
        Createur::create([
            'nom' => 'Océane Lefebvre',
            'email' => 'oceane.lefebvre@yahoo.fr',
            'pseudo_tiktok' => '@oceane.lfb',
            'statut' => 'En attente',
            'equipe_id' => $equipe?->id,
            'agent_id' => $agent?->id,
            'ambassadeur_id' => $ambassadeur?->id,
            'stats_vues' => 210000,
            'stats_followers' => 78000,
        ]);
    }
}
