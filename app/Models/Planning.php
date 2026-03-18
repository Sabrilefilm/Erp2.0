<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Planning extends Model
{
    protected $table = 'planning';

    public const STATUT_PROGRAMME = 'programme';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_ACCEPTEE = 'acceptee';
    public const STATUT_MANQUE = 'manque';
    public const STATUT_REFUSEE = 'refusee';

    public static function statutLabels(): array
    {
        return [
            self::STATUT_PROGRAMME => 'Programmé',
            self::STATUT_EN_COURS   => 'En cours',
            self::STATUT_ACCEPTEE   => 'Acceptée',
            self::STATUT_MANQUE     => 'Manqué',
            self::STATUT_REFUSEE    => 'Refusée',
        ];
    }

    /** Niveaux du match (pour type match_off) : tranches de vues / followers */
    public const NIVEAUX_MATCH_OFF = [
        '0_5k' => '0 à 5 K',
        '0_10k' => '0 à 10 K',
        '10_20k' => '10 à 20 K',
        '20_30k' => '20 à 30 K',
        '30_50k' => '30 à 50 K',
        '50_100k' => '50 à 100 K',
        '100k_500k' => '100 K à 500 K',
        '500k_5m' => '500 K à 5 M',
        'sans_limite' => 'Sans limite',
    ];

    protected $fillable = [
        'createur_id',
        'date',
        'heure',
        'type',
        'niveau_match',
        'avec_boost',
        'statut',
        'raison',
        'createur_adverse',
        'createur_adverse_agence',
        'createur_adverse_agent',
        'createur_adverse_numero',
        'createur_adverse_at',
        'createur_adverse_email',
        'createur_adverse_autres',
        'cree_par',
        'updated_par',
        'rappel_sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'avec_boost' => 'boolean',
        'rappel_sent_at' => 'datetime',
    ];

    /** Consignes : match officiel sans boost ou avec boost */
    public function getConsignesLabelAttribute(): string
    {
        return $this->avec_boost ? 'Match officiel avec boost' : 'Match officiel sans boost';
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function updatedPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_par');
    }
}
