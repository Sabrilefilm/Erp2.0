<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreateurAdverse extends Model
{
    protected $table = 'createurs_adverses';

    protected $fillable = [
        'tiktok_at',
        'nom',
        'agence',
        'agent',
        'telephone',
        'email',
        'autres_infos',
    ];

    /**
     * Normalise le @ TikTok pour la recherche (sans @, minuscules, trim).
     */
    public static function normalizeAt(?string $at): string
    {
        if ($at === null || $at === '') {
            return '';
        }
        $at = preg_replace('/^@+/', '', trim($at));

        return mb_strtolower($at);
    }
}
