<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_FONDATEUR = 'fondateur';
    public const ROLE_DIRECTEUR = 'directeur';
    public const ROLE_SOUS_DIRECTEUR = 'sous_directeur';
    public const ROLE_MANAGEUR = 'manageur';
    public const ROLE_SOUS_MANAGER = 'sous_manager';
    public const ROLE_AGENT = 'agent';
    public const ROLE_AMBASSADEUR = 'ambassadeur';
    public const ROLE_CREATEUR = 'createur';

    /** Hiérarchie : index plus bas = plus de pouvoir */
    public const ROLE_LEVELS = [
        self::ROLE_FONDATEUR => 1,
        self::ROLE_DIRECTEUR => 2,
        self::ROLE_SOUS_DIRECTEUR => 3,
        self::ROLE_MANAGEUR => 4,
        self::ROLE_SOUS_MANAGER => 5,
        self::ROLE_AGENT => 6,
        self::ROLE_AMBASSADEUR => 7,
        self::ROLE_CREATEUR => 8,
    ];

    /** Libellés d'affichage des rôles */
    public const ROLE_LABELS = [
        self::ROLE_FONDATEUR => 'Fondateur',
        self::ROLE_DIRECTEUR => 'Directeur',
        self::ROLE_SOUS_DIRECTEUR => 'Sous-directeur',
        self::ROLE_MANAGEUR => 'Manageur',
        self::ROLE_SOUS_MANAGER => 'Sous-manager',
        self::ROLE_AGENT => 'Agent',
        self::ROLE_AMBASSADEUR => 'Ambassadeur',
        self::ROLE_CREATEUR => 'Créateur',
    ];

    public function getRoleLabel(): string
    {
        if ($this->role === self::ROLE_FONDATEUR) {
            return $this->isFondateurPrincipal() ? 'Fondateur Global' : 'Fondateur d\'Agence';
        }
        return self::ROLE_LABELS[$this->role] ?? str_replace('_', ' ', $this->role);
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'date_naissance',
        'password',
        'role',
        'is_fondateur_principal',
        'equipe_id',
        'manager_id',
        'compte_bloque',
        'login_blocked_until',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Vérifie si aujourd'hui est l'anniversaire de l'utilisateur (jour + mois). */
    public function isBirthdayToday(): bool
    {
        if (! $this->date_naissance) {
            return false;
        }
        $d = $this->date_naissance instanceof \Carbon\Carbon
            ? $this->date_naissance
            : \Carbon\Carbon::parse($this->date_naissance);

        return $d->isBirthday(now());
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_naissance' => 'date',
            'password' => 'hashed',
            'compte_bloque' => 'boolean',
            'is_fondateur_principal' => 'boolean',
            'login_blocked_until' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    /** Compte temporairement bloqué après trop de tentatives de connexion (anti-bruteforce). */
    public function isLoginBlocked(): bool
    {
        return $this->login_blocked_until && $this->login_blocked_until->isFuture();
    }

    /** Fondateur principal (toi) : accès à tout, vue générale, plein pouvoir. */
    public function isFondateurPrincipal(): bool
    {
        return $this->is_fondateur_principal === true;
    }

    /** Fondateur d’une sous-agence : rôle fondateur mais limité à son équipe, pas la vue générale ni le pouvoir principal. */
    public function estFondateurSousAgence(): bool
    {
        return $this->isFondateur() && ! $this->isFondateurPrincipal();
    }

    /**
     * Id de l'équipe (agence) à laquelle l'utilisateur est limité, ou null s'il voit tout.
     * Fondateur d'agence = son équipe ; Directeur/Sous-directeur avec equipe_id = son agence.
     */
    public function scopeToAgenceEquipeId(): ?int
    {
        if ($this->estFondateurSousAgence() && $this->equipe_id) {
            return $this->equipe_id;
        }
        if (($this->isDirecteur() || $this->isSousDirecteur()) && $this->equipe_id) {
            return $this->equipe_id;
        }
        return null;
    }

    public function isFondateur(): bool
    {
        if (strtolower((string) $this->role) === self::ROLE_FONDATEUR) {
            return true;
        }
        return $this->is_fondateur_principal === true;
    }

    public function isDirecteur(): bool
    {
        return $this->role === self::ROLE_DIRECTEUR;
    }

    public function isSousDirecteur(): bool
    {
        return $this->role === self::ROLE_SOUS_DIRECTEUR;
    }

    public function isManageur(): bool
    {
        return $this->role === self::ROLE_MANAGEUR;
    }

    /** Fondateurs, directeurs et manageurs peuvent ajouter des entrées (règles, formations, récompenses, demandes de retrait). */
    public function canAddEntries(): bool
    {
        return $this->isFondateur() || $this->isDirecteur() || $this->isManageur();
    }

    /** Attribuer une récompense et récupérer les informations : seul le fondateur principal. */
    public function canAttribuerOuRecupererRecompense(): bool
    {
        return $this->isFondateurPrincipal();
    }

    public function isSousManager(): bool
    {
        return $this->role === self::ROLE_SOUS_MANAGER;
    }

    public function isAgent(): bool
    {
        return $this->role === self::ROLE_AGENT;
    }

    public function isAmbassadeur(): bool
    {
        return $this->role === self::ROLE_AMBASSADEUR;
    }

    public function isCreateur(): bool
    {
        return $this->role === self::ROLE_CREATEUR;
    }

    /** Peut voir la page Score d'intégrité (individuel ou liste équipe). Fondateur principal va en gestion. */
    public function canSeeScoreIntegrite(): bool
    {
        return $this->isCreateur()
            || $this->isAgent()
            || $this->isManageur()
            || $this->isSousManager()
            || $this->isDirecteur()
            || $this->isSousDirecteur()
            || $this->estFondateurSousAgence();
    }

    /** Le rapport de la semaine est obligatoire pour tous sauf les fondateurs et les créateurs. */
    public function doitRemplirRapportVendredi(): bool
    {
        return ! $this->isFondateur() && ! $this->isCreateur();
    }

    /** Seuls les fondateurs (Global et d'Agence) voient tous les rapports de la semaine pour la traçabilité et l'accompagnement. */
    public function canVoirTousRapportsVendredi(): bool
    {
        return $this->isFondateur();
    }

    /** Peut programmer un match (P-Match) : fondateurs (global + sous-agence), directeurs, manageurs, agents. Pas les créateurs ni ambassadeurs. */
    public function canProgrammerMatch(): bool
    {
        if ($this->isFondateur()) {
            return true;
        }
        return ! $this->isCreateur() && ! $this->isAmbassadeur();
    }

    /** Niveau du rôle (1 = plus haut) */
    public function roleLevel(): int
    {
        return self::ROLE_LEVELS[$this->role] ?? 99;
    }

    /** Vérifie si cet utilisateur a un niveau >= au rôle donné */
    public function hasRoleOrAbove(string $role): bool
    {
        $level = self::ROLE_LEVELS[$role] ?? 99;
        return $this->roleLevel() <= $level;
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class, 'equipe_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function subordonnes()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /** Fiche créateur liée (si cet utilisateur est un créateur). */
    public function createur()
    {
        return $this->hasOne(Createur::class);
    }

    /**
     * Retourne la fiche créateur pour cet utilisateur (rôle créateur).
     * Cherche d'abord par user_id, puis par email. Si trouvée par email, met à jour
     * user_id sur la fiche pour que la relation createur() et l'affichage agent/équipe restent à jour.
     */
    public function getCreateurFiche(): ?Createur
    {
        if (! $this->isCreateur()) {
            return null;
        }
        $fiche = Createur::where('user_id', $this->id)->first();
        if ($fiche) {
            return $fiche;
        }
        if (! $this->email) {
            return null;
        }
        $fiche = Createur::where('email', $this->email)->first();
        if ($fiche && $fiche->user_id !== $this->id) {
            $fiche->update(['user_id' => $this->id]);
        }
        return $fiche;
    }

    /** Créateurs dont cet utilisateur est responsable (agent/sous-manager) */
    public function createursGeres()
    {
        return $this->hasMany(Createur::class, 'agent_id');
    }

    public function rapportVendredis()
    {
        return $this->hasMany(RapportVendredi::class);
    }
}
