<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Createur extends Model
{
    protected $fillable = [
        'nom',
        'user_id',
        'email',
        'pseudo_tiktok',
        'statut',
        'equipe_id',
        'est_partenaire',
        'agent_id',
        'ambassadeur_id',
        'notes',
        'missions',
        'stats_vues',
        'stats_followers',
        'stats_engagement',
        'heures_mois',
        'jours_mois',
        'diamants',
        'date_import',
        'contrat_signe_le',
        'reglement_accepte_le',
    ];

    protected $casts = [
        'date_import' => 'datetime',
        'contrat_signe_le' => 'datetime',
        'reglement_accepte_le' => 'datetime',
        'est_partenaire' => 'boolean',
        'stats_vues' => 'integer',
        'stats_followers' => 'integer',
        'stats_engagement' => 'decimal:2',
        'heures_mois' => 'float',
        'jours_mois' => 'integer',
        'diamants' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipe(): BelongsTo
    {
        return $this->belongsTo(Equipe::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function ambassadeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ambassadeur_id');
    }

    public function planning(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    public function recompenses(): HasMany
    {
        return $this->hasMany(Recompense::class);
    }

    public function sanctions(): HasMany
    {
        return $this->hasMany(Sanction::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function statsMensuelles(): HasMany
    {
        return $this->hasMany(CreateurStatMensuelle::class)->orderByDesc('annee')->orderByDesc('mois');
    }

    public function demandesMatch(): HasMany
    {
        return $this->hasMany(DemandeMatch::class);
    }

    /** Catalogue Faucheuse Agency (Match Partenaire) : agence avec "Faucheuse" dans le nom OU marquée partenaire. Exclut les créateurs forcés en Unions (est_partenaire = false). */
    public function scopeFaucheuseAgency($query): void
    {
        $query->where(fn ($q) => $q->whereNull('est_partenaire')->orWhere('est_partenaire', true))
            ->whereHas('equipe', fn ($eq) => $eq->where(function ($eq2) {
                $eq2->whereRaw('LOWER(nom) LIKE ?', ['%faucheuse%'])->orWhere('est_partenaire', true);
            }));
    }

    /** Catalogue Unions Agency (Match Unions) : créateur forcé Unions (est_partenaire = false), sans équipe, ou dont l'agence n'est pas Faucheuse. */
    public function scopeUnionsAgency($query): void
    {
        $query->where(function ($q) {
            $q->where('est_partenaire', false)
                ->orWhereNull('equipe_id')
                ->orWhereHas('equipe', fn ($eq) => $eq->whereRaw('LOWER(nom) NOT LIKE ?', ['%faucheuse%'])
                    ->where(function ($eq2) {
                        $eq2->where('est_partenaire', false)->orWhereNull('est_partenaire');
                    }));
        });
    }
}
