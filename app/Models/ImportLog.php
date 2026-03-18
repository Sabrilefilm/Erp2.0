<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $table = 'import_logs';

    protected $fillable = [
        'user_id',
        'fichier',
        'statut',
        'lignes_importees',
        'lignes_erreur',
        'message',
        'log_detail',
    ];

    protected $casts = [
        'lignes_importees' => 'integer',
        'lignes_erreur' => 'integer',
        'log_detail' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
