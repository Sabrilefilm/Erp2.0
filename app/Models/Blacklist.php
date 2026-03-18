<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blacklist extends Model
{
    protected $table = 'blacklist';

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'phone',
        'raison',
        'ajoute_par',
    ];

    public function ajoutePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ajoute_par');
    }
}
