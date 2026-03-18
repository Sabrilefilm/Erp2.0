<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regle extends Model
{
    protected $fillable = [
        'titre',
        'contenu',
        'ordre',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];
}
