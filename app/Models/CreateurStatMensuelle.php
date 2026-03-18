<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreateurStatMensuelle extends Model
{
    protected $table = 'createur_stats_mensuelles';

    protected $fillable = [
        'createur_id',
        'annee',
        'mois',
        'jours_stream',
        'heures_stream',
        'diamants',
    ];

    protected $casts = [
        'annee' => 'integer',
        'mois' => 'integer',
        'jours_stream' => 'integer',
        'heures_stream' => 'float',
        'diamants' => 'integer',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }

    /** Libellé court pour affichage (ex. "Février 2025") */
    public function getLibelleAttribute(): string
    {
        $noms = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return ($noms[$this->mois] ?? $this->mois) . ' ' . $this->annee;
    }
}
