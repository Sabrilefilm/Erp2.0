<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreIntegriteHistorique extends Model
{
    protected $table = 'score_integrite_historique';

    protected $fillable = [
        'createur_id',
        'heure_modification',
        'details_infraction',
        'score_avant',
        'score_consequent',
        'sanction_infraction',
    ];

    protected $casts = [
        'heure_modification' => 'datetime',
    ];

    /** Score maximum possible */
    public const SCORE_MAX = 100;

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }
}
