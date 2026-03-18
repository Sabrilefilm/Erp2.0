<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateFondateurSabri extends Command
{
    protected $signature = 'fondateur:sabri';

    protected $description = 'Crée ou met à jour le compte fondateur Sabri pour Unions (utilisateur: sabri, mot de passe: Aubagne@13400)';

    public function handle(): int
    {
        // Mot de passe en clair : le cast "hashed" du modèle User le hache à l'enregistrement.
        User::where('role', User::ROLE_FONDATEUR)->update(['is_fondateur_principal' => false]);
        User::updateOrCreate(
            ['username' => 'sabri'],
            [
                'name' => 'Sabri',
                'email' => 'sabri@agence.local',
                'password' => 'Aubagne@13400',
                'role' => User::ROLE_FONDATEUR,
                'is_fondateur_principal' => true,
            ]
        );

        $this->info('Compte fondateur Sabri créé ou mis à jour.');
        $this->line('Connexion : utilisateur <comment>sabri</comment> / mot de passe <comment>Aubagne@13400</comment>');
        return self::SUCCESS;
    }
}
