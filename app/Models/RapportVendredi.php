<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapportVendredi extends Model
{
    protected $table = 'rapport_vendredis';

    protected $fillable = [
        'user_id',
        'annee',
        'semaine',
        'contenu',
        'valide_at',
        'valide_par',
    ];

    protected function casts(): array
    {
        return [
            'annee' => 'integer',
            'semaine' => 'integer',
            'valide_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function isValide(): bool
    {
        return $this->valide_at !== null;
    }

    /** Libellé court pour la semaine (ex. "Semaine du 7 mars 2026") */
    public function getLibelleSemaineAttribute(): string
    {
        $date = now()->setISODate($this->annee, $this->semaine)->startOfWeek();

        return 'Semaine du ' . $date->format('d/m/Y');
    }
}
