<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreFideliteAction extends Model
{
    protected $table = 'score_fidelite_actions';

    public $timestamps = false;

    protected $fillable = [
        'createur_id',
        'annee',
        'mois',
        'action_type',
        'points',
        'source_type',
        'source_id',
        'raison',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }
}
