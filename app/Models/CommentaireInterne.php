<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentaireInterne extends Model
{
    protected $table = 'commentaires_internes';

    protected $fillable = ['createur_id', 'user_id', 'contenu'];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
