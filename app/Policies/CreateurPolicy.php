<?php

namespace App\Policies;

use App\Models\Createur;
use App\Models\User;

class CreateurPolicy
{
    /**
     * Voir un créateur : fondateur principal/directeur/sous-directeur tout ; fondateur sous-agence/sous-manager/agent leur périmètre.
     */
    public function view(User $user, Createur $createur): bool
    {
        if ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
            return true;
        }
        if ($user->estFondateurSousAgence()) {
            return $createur->equipe_id === $user->equipe_id;
        }
        if ($user->isSousManager()) {
            return $createur->equipe_id === $user->equipe_id;
        }
        if ($user->isAgent()) {
            return $createur->agent_id === $user->id;
        }
        if ($user->isAmbassadeur()) {
            return $createur->ambassadeur_id === $user->id;
        }
        if ($user->isCreateur()) {
            return $createur->email === $user->email || $createur->id === $user->id;
        }
        return false;
    }

    /**
     * Modifier les données officielles (Jours, Heures, Diamants) : fondateur principal uniquement, via import Excel.
     */
    public function updateDonneesOfficielles(User $user): bool
    {
        return $user->isFondateurPrincipal();
    }

    /**
     * Modifier notes/statut : fondateur principal tout ; fondateur sous-agence ou agent selon périmètre.
     */
    public function update(User $user, Createur $createur): bool
    {
        if ($user->isFondateurPrincipal()) {
            return true;
        }
        if ($user->estFondateurSousAgence() && $createur->equipe_id === $user->equipe_id) {
            return true;
        }
        // Agent peut mettre à jour statuts/notes pour ses créateurs
        if ($user->isAgent() && $createur->agent_id === $user->id) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Createur $createur): bool
    {
        return $user->isFondateurPrincipal();
    }
}
