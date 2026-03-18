<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeMatch extends Model
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_PROGRAMMEE = 'programmee';
    public const STATUT_REFUSEE = 'refusee';

    protected $table = 'demandes_match';

    protected $fillable = [
        'createur_id',
        'date_souhaitee',
        'heure_souhaitee',
        'type',
        'qui_en_face',
        'message',
        'statut',
    ];

    protected $casts = [
        'date_souhaitee' => 'date',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }
}
