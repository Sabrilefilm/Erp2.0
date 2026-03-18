<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormationQuizTentative extends Model
{
    protected $fillable = [
        'user_id',
        'formation_id',
        'score',
        'total',
        'difficulte',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function getPourcentageAttribute(): ?float
    {
        if ($this->total <= 0) {
            return null;
        }
        return round((float) ($this->score / $this->total) * 100, 1);
    }
}
