<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'contenu', 'fichier_path', 'fichier_nom', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopeBetweenUsers($query, $userIdA, $userIdB)
    {
        return $query->where(function ($q) use ($userIdA, $userIdB) {
            $q->where('sender_id', $userIdA)->where('receiver_id', $userIdB)
                ->orWhere('sender_id', $userIdB)->where('receiver_id', $userIdA);
        });
    }

    public function scopeUnreadFor($query, $userId)
    {
        return $query->where('receiver_id', $userId)->whereNull('read_at');
    }
}
