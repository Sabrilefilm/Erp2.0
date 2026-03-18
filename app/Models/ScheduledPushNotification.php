<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledPushNotification extends Model
{
    public const TARGET_USER = 'user';
    public const TARGET_ROLE = 'role';
    public const TARGET_ALL = 'all';

    protected $fillable = [
        'title',
        'body',
        'target_type',
        'target_value',
        'send_at',
        'sent_at',
        'created_by',
        'template_key',
    ];

    protected function casts(): array
    {
        return [
            'send_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
