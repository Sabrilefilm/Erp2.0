<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetFondateurPrincipal extends Command
{
    protected $signature = 'fondateur:set-principal {email? : Email du fondateur à définir comme principal (optionnel)}';

    protected $description = 'Définit le Fondateur global (is_fondateur_principal). Sans argument : le premier fondateur par id. Avec email : ce fondateur devient principal.';

    public function handle(): int
    {
        $email = $this->argument('email');

        if ($email) {
            $user = User::where('role', User::ROLE_FONDATEUR)->where('email', $email)->first();
            if (! $user) {
                $this->error("Aucun fondateur avec l'email : {$email}");
                return self::FAILURE;
            }
        } else {
            $user = User::where('role', User::ROLE_FONDATEUR)->orderBy('id')->first();
            if (! $user) {
                $this->error('Aucun utilisateur avec le rôle fondateur.');
                return self::FAILURE;
            }
        }

        User::where('role', User::ROLE_FONDATEUR)->update(['is_fondateur_principal' => false]);
        $user->update(['is_fondateur_principal' => true]);

        $this->info("Fondateur principal défini : {$user->name} ({$user->email}).");
        return self::SUCCESS;
    }
}
