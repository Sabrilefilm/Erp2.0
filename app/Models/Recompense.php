<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recompense extends Model
{
    use SoftDeletes;
    /** Modes de réception : TikTok (cadeau, -50 %), Carte cadeau (code), Virement (RIB) */
    public const TYPE_TIKTOK = 'tiktok';
    public const TYPE_CARTE_CADEAU = 'carte_cadeau';
    public const TYPE_VIREMENT = 'virement';

    public const TYPES = [
        self::TYPE_TIKTOK => 'Cadeau TikTok',
        self::TYPE_CARTE_CADEAU => 'Carte cadeau',
        self::TYPE_VIREMENT => 'Virement bancaire',
    ];

    /** Montants possibles pour une carte cadeau (en €). Affichés selon le solde disponible. */
    public const MONTANTS_CARTE_CADEAU = [5, 10, 15, 20, 25, 30, 50, 100, 200, 500];

    /** Types de carte cadeau (enseigne) — clé = valeur en base, valeur = libellé affiché */
    public const TYPES_CARTE_CADEAU = [
        'multi_enseignes' => 'Carte cadeau Multi-Enseignes',
        'expedia' => 'Carte Cadeau d\'Hôtel (Expedia)',
        'zalando' => 'Carte cadeau Zalando',
        'fortnite' => 'Carte cadeau Fortnite',
        'ikea' => 'Carte cadeau IKEA',
        'hm' => 'Carte cadeau H&M',
        'maisons_du_monde' => 'Carte cadeau Maisons du Monde',
        'xbox' => 'Carte cadeau Xbox',
        'playstation' => 'Carte cadeau PlayStation',
    ];

    /** TikTok prélève 50 % : le créateur reçoit la moitié en valeur cadeau */
    public const TIKTOK_POURCENTAGE_CREATEUR = 50;

    /** Statut : le fondateur a attribué le montant, le créateur doit encore choisir le type (virement / TikTok / carte cadeau) */
    public const STATUT_EN_ATTENTE_CHOIX = 'en_attente_choix';

    /** Statut : la récompense a été refusée par le fondateur (avec motif) */
    public const STATUT_REFUSEE = 'refusee';

    protected $fillable = [
        'createur_id',
        'type',
        'montant',
        'raison',
        'attribue_par',
        'statut',
        'motif_refus',
        'date_cadeau_tiktok',
        'heure_cadeau_tiktok',
        'code_cadeau',
        'type_carte_cadeau',
        'montant_carte_cadeau',
        'quantite_carte_cadeau',
        'facture_disponible_at',
        'rib_nom',
        'rib_prenom',
        'rib_iban',
        'rib_banque',
        'rib_confirme',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'montant_carte_cadeau' => 'decimal:2',
        'rib_confirme' => 'boolean',
        'facture_disponible_at' => 'datetime',
    ];

    /** La facture est téléchargeable si le mode est choisi et que le délai (anti-surcharge) est passé. */
    public function factureEstDisponible(): bool
    {
        if ($this->isEnAttenteChoix()) {
            return false;
        }
        if ($this->facture_disponible_at === null) {
            return true; // anciennes récompenses sans délai
        }
        return now()->gte($this->facture_disponible_at);
    }

    /** Secondes restantes avant disponibilité (0 si déjà disponible). */
    public function secondesRestantesFacture(): int
    {
        if ($this->factureEstDisponible() || $this->facture_disponible_at === null) {
            return 0;
        }
        return (int) max(0, $this->facture_disponible_at->getTimestamp() - now()->getTimestamp());
    }

    public function isEnAttenteChoix(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE_CHOIX || $this->type === null;
    }

    /** Montant effectivement reçu en cadeau TikTok (50 % du montant). Uniquement pour type TikTok. */
    public function getMontantTiktokAttribute(): float
    {
        if ($this->type !== self::TYPE_TIKTOK && $this->type !== 'TikTok') {
            return (float) $this->montant;
        }
        return round((float) $this->montant * (self::TIKTOK_POURCENTAGE_CREATEUR / 100), 2);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }

    public function attribuePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attribue_par');
    }
}
