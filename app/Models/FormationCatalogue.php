<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormationCatalogue extends Model
{
    protected $fillable = ['slug', 'label', 'ordre'];

    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class, 'catalogue', 'slug');
    }

    /** Slug généré à partir du label (pour création). */
    public static function slugFromLabel(string $label): string
    {
        $slug = preg_replace('/[^a-z0-9]+/i', '_', trim($label));
        $slug = strtolower(trim($slug, '_'));
        return $slug ?: 'catalogue_' . substr(uniqid(), -6);
    }
}
