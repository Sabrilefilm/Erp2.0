<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    public const TYPE_VIDEO = 'video';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_LIEN = 'lien';

    public const TYPES = [
        self::TYPE_VIDEO => 'Vidéo',
        self::TYPE_DOCUMENT => 'Document',
        self::TYPE_LIEN => 'Lien',
    ];

    public const CATALOGUE_TIKTOK = 'tiktok';
    public const CATALOGUE_PROJET_PERSONNEL = 'projet_personnel';
    public const CATALOGUE_AUTRES = 'autres';

    public const CATALOGUES = [
        self::CATALOGUE_TIKTOK => 'Découvrir TikTok',
        self::CATALOGUE_PROJET_PERSONNEL => 'Projet personnel',
        self::CATALOGUE_AUTRES => 'Autres',
    ];

    protected $fillable = [
        'titre',
        'description',
        'mots_cles',
        'type',
        'catalogue',
        'url',
        'media_path',
        'fichier_path',
        'fichier_nom',
        'ordre',
        'actif',
    ];

    /** Indique si le média uploadé est une vidéo (extension courante). */
    public function isMediaVideo(): bool
    {
        if (! $this->media_path) {
            return false;
        }
        $ext = strtolower(pathinfo($this->media_path, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'webm', 'ogg', 'mov'], true);
    }

    /** Indique si le média uploadé est une image. */
    public function isMediaImage(): bool
    {
        if (! $this->media_path) {
            return false;
        }
        $ext = strtolower(pathinfo($this->media_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    public function questions()
    {
        return $this->hasMany(FormationQuestion::class, 'formation_id');
    }

    public function tentatives()
    {
        return $this->hasMany(FormationQuizTentative::class, 'formation_id');
    }

    protected $casts = [
        'actif' => 'boolean',
    ];

    /** Retourne l'ID YouTube depuis une URL (youtube.com/watch?v=, youtu.be/, embed). */
    public static function youtubeIdFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }
        return null;
    }
}
