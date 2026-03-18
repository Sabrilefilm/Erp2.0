<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'createur_id',
        'amount',
        'type',
        'status',
        'notes',
        'details',
        'traite_par',
        'traite_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'details' => 'array',
        'traite_at' => 'datetime',
    ];

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }
}
