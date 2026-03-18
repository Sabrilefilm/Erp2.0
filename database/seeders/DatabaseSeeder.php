<?php

namespace Database\Seeders;

use App\Models\Createur;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Mots de passe en clair : le cast 'hashed' du modèle User les hache à l'enregistrement.
        // Ne pas utiliser Hash::make() ici sinon double hachage = connexion impossible.
        $fondateur = User::create([
            'name' => 'Sabri',
            'username' => 'sabri',
            'email' => 'sabri@agence.local',
            'password' => 'Aubagne@13400',
            'role' => User::ROLE_FONDATEUR,
        ]);

        $equipe = Equipe::create(['nom' => 'Équipe 1']);

        User::create([
            'name' => 'Directeur',
            'username' => 'directeur',
            'email' => 'directeur@agence.local',
            'password' => 'password',
            'role' => User::ROLE_DIRECTEUR,
        ]);

        $sousManager = User::create([
            'name' => 'Sous-Manager',
            'username' => 'sousmanager',
            'email' => 'sousmanager@agence.local',
            'password' => 'password',
            'role' => User::ROLE_SOUS_MANAGER,
            'equipe_id' => $equipe->id,
        ]);

        $equipe->update(['manager_id' => $sousManager->id]);

        $agent = User::create([
            'name' => 'Agent',
            'username' => 'agent',
            'email' => 'agent@agence.local',
            'password' => 'password',
            'role' => User::ROLE_AGENT,
            'equipe_id' => $equipe->id,
            'manager_id' => $sousManager->id,
        ]);

        $ambassadeur = User::create([
            'name' => 'Ambassadeur',
            'username' => 'ambassadeur',
            'email' => 'ambassadeur@agence.local',
            'password' => 'password',
            'role' => User::ROLE_AMBASSADEUR,
        ]);

        User::create([
            'name' => 'Créateur',
            'username' => 'createur',
            'email' => 'createur@agence.local',
            'password' => 'password',
            'role' => User::ROLE_CREATEUR,
        ]);

        // Créateurs (liste TikTok / influenceurs) pour la page Créateurs — seulement si la table est vide
        if (Createur::count() === 0) {
        Createur::create([
            'nom' => 'Inès Saidi',
            'email' => 'ines.saidi@outlook.com',
            'pseudo_tiktok' => '@inessaidi',
            'statut' => 'Actif',
            'equipe_id' => $equipe->id,
            'agent_id' => $agent->id,
            'ambassadeur_id' => $ambassadeur->id,
            'stats_vues' => 125000,
            'stats_followers' => 45000,
        ]);
        Createur::create([
            'nom' => 'Kylian Petit',
            'email' => 'kylian.petit@gmail.com',
            'pseudo_tiktok' => '@kylian.pt',
            'statut' => 'Actif',
            'equipe_id' => $equipe->id,
            'agent_id' => $agent->id,
            'stats_vues' => 89000,
            'stats_followers' => 32000,
        ]);
        Createur::create([
            'nom' => 'Océane Lefebvre',
            'email' => 'oceane.lefebvre@yahoo.fr',
            'pseudo_tiktok' => '@oceane.lfb',
            'statut' => 'En attente',
            'equipe_id' => $equipe->id,
            'agent_id' => $agent->id,
            'ambassadeur_id' => $ambassadeur->id,
            'stats_vues' => 210000,
            'stats_followers' => 78000,
        ]);
        }
    }
}
