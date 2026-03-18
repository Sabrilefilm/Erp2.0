<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipe extends Model
{
    protected $fillable = ['nom', 'est_partenaire', 'manager_id'];

    protected function casts(): array
    {
        return [
            'est_partenaire' => 'boolean',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function membres(): HasMany
    {
        return $this->hasMany(User::class, 'equipe_id');
    }

    /** Agents et ambassadeurs de cette équipe (pour affichage "Agent" dans l'interface). */
    public function agents(): HasMany
    {
        return $this->hasMany(User::class, 'equipe_id')
            ->whereIn('role', [User::ROLE_AGENT, User::ROLE_AMBASSADEUR]);
    }

    public function createurs(): HasMany
    {
        return $this->hasMany(Createur::class, 'equipe_id');
    }
}
