<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Créer comptes : fondateur, directeur, sous-directeur, manageur, sous-manager (selon hiérarchie).
     */
    public function create(User $user): bool
    {
        return $user->isFondateur() || $user->isDirecteur() || $user->isSousDirecteur()
            || $user->isManageur() || $user->isSousManager();
    }

    /**
     * Voir un utilisateur : selon hiérarchie.
     * Fondateur principal : tout. Fondateur sous-agence : uniquement son équipe.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->isFondateurPrincipal()) {
            return true;
        }
        if ($user->estFondateurSousAgence()) {
            return ($model->equipe_id === $user->equipe_id || $model->id === $user->id)
                && ! $model->isFondateurPrincipal();
        }
        if ($user->isDirecteur() || $user->isSousDirecteur()) {
            return ! $model->isFondateur();
        }
        if ($user->isManageur() || $user->isSousManager()) {
            return $model->equipe_id === $user->equipe_id || $model->id === $user->id;
        }
        if ($user->isAgent()) {
            return $model->manager_id === $user->id || $model->id === $user->id;
        }
        return $model->id === $user->id;
    }

    /**
     * Modifier : selon hiérarchie. Fondateur principal : tout. Fondateur sous-agence : son équipe uniquement.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->isFondateurPrincipal()) {
            return true;
        }
        if ($user->estFondateurSousAgence()) {
            return $model->equipe_id === $user->equipe_id && $model->id !== $user->id
                ? $model->roleLevel() >= $user->roleLevel()
                : $model->id === $user->id;
        }
        if ($user->isDirecteur() || $user->isSousDirecteur()) {
            return ! $model->isFondateur() && $model->roleLevel() >= $user->roleLevel();
        }
        if (($user->isManageur() || $user->isSousManager()) && $model->equipe_id === $user->equipe_id) {
            return $model->roleLevel() >= $user->roleLevel();
        }
        return false;
    }

    /**
     * Supprimer : fondateur principal pour tous ; directeur/sous-directeur en dessous.
     * Fondateur sous-agence et manageur/sous-manager ne peuvent pas supprimer de compte.
     */
    public function delete(User $user, User $model): bool
    {
        if ($model->isFondateurPrincipal()) {
            return $user->id === $model->id;
        }
        if ($model->isFondateur()) {
            return $user->isFondateurPrincipal();
        }
        if ($user->isFondateurPrincipal()) {
            return true;
        }
        if (($user->isDirecteur() || $user->isSousDirecteur()) && $model->roleLevel() > $user->roleLevel()) {
            return true;
        }
        return false;
    }
}
