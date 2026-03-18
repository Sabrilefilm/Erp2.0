<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'contenu',
        'type',
        'ordre',
        'actif',
        'date_evenement',
        'lieu_evenement',
        'lien_tiktok',
        'hashtag_principal',
        'objectif_campagne',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'date_evenement' => 'datetime',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    // Types d'annonces
    const TYPE_ANNONCE = 'annonce';
    const TYPE_EVENEMENT = 'evenement';
    const TYPE_CAMPAGNE = 'campagne';

    public static function getTypes()
    {
        return [
            self::TYPE_ANNONCE => 'Annonce générale',
            self::TYPE_EVENEMENT => 'Événement TikTok',
            self::TYPE_CAMPAGNE => 'Campagne TikTok',
        ];
    }

    public function getTypeLabelAttribute()
    {
        $types = self::getTypes();
        return $types[$this->type] ?? $this->type;
    }

    public function scopeActives($query)
    {
        return $query->where('actif', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrderByOrdre($query)
    {
        return $query->orderBy('ordre', 'asc')->orderBy('created_at', 'desc');
    }
}
