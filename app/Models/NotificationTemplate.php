<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    public const KEY_MATCH_OFF_REMINDER = 'match_off_reminder';
    public const KEY_RAPPEL_LIVE = 'rappel_live';
    public const KEY_OBJECTIF_ATTEINT = 'objectif_atteint';
    public const KEY_ANNONCE = 'annonce';

    protected $fillable = [
        'key',
        'label',
        'title',
        'body',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }
}
