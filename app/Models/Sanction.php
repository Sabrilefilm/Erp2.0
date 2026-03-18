<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sanction extends Model
{
    protected $fillable = [
        'createur_id',
        'type',
        'niveau',
        'raison',
        'attribue_par',
        'statut',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }

    public function attribuePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attribue_par');
    }
}
