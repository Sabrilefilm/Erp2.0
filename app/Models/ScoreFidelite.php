<?php

namespace App\Models;

use App\Notifications\RecompenseAttribueeNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScoreFidelite extends Model
{
    protected $table = 'score_fidelite';

    protected $fillable = [
        'createur_id',
        'annee',
        'mois',
        'score',
        'palier_80_debloque_at',
        'palier_100_debloque_at',
    ];

    protected $casts = [
        'palier_80_debloque_at' => 'datetime',
        'palier_100_debloque_at' => 'datetime',
    ];

    public const SCORE_MAX = 100;

    /** Palier 80 % = carte cadeau 15 € */
    public const PALIER_80_POINTS = 80;
    public const PALIER_80_MONTANT = 15;

    /** Palier 100 % = carte cadeau 35 € */
    public const PALIER_100_POINTS = 100;
    public const PALIER_100_MONTANT = 35;

    /** Type d'action : fidélité des matchs (venir à un match) */
    public const ACTION_FIDELITE_MATCH = 'fidelite_match';
    public const ACTION_FIDELITE_MATCH_POINTS = 3;

    /** Actions manuelles (ajout/retrait/set depuis l'interface) */
    public const ACTION_MANUAL_ADD = 'manual_add';
    public const ACTION_MANUAL_REMOVE = 'manual_remove';
    public const ACTION_MANUAL_SET = 'manual_set';

    public static function actionLabels(): array
    {
        return [
            self::ACTION_FIDELITE_MATCH => 'Fidélité des matchs (match honoré)',
            self::ACTION_MANUAL_ADD => 'Ajout manuel',
            self::ACTION_MANUAL_REMOVE => 'Retrait manuel',
            self::ACTION_MANUAL_SET => 'Score mis à jour',
        ];
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Createur::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ScoreFideliteAction::class, 'createur_id', 'createur_id');
    }

    /** Récupère ou crée le score « Bonnes actions » d'un créateur pour le mois donné (défaut = mois courant). Le 1er du mois = nouveau mois donc score 0. */
    public static function getOrCreateForCreateur(int $createurId, ?int $annee = null, ?int $mois = null): self
    {
        $annee = $annee ?? (int) now()->format('Y');
        $mois = $mois ?? (int) now()->format('n');
        $row = self::where('createur_id', $createurId)->where('annee', $annee)->where('mois', $mois)->first();
        if ($row) {
            return $row;
        }
        return self::create([
            'createur_id' => $createurId,
            'annee' => $annee,
            'mois' => $mois,
            'score' => 0,
        ]);
    }

    /** Score du mois courant pour un créateur (0 si pas encore de ligne). */
    public static function getScoreForCreateurMois(int $createurId, ?int $annee = null, ?int $mois = null): int
    {
        $annee = $annee ?? (int) now()->format('Y');
        $mois = $mois ?? (int) now()->format('n');
        $row = self::where('createur_id', $createurId)->where('annee', $annee)->where('mois', $mois)->first();
        return $row ? $row->score : 0;
    }

    /**
     * Ajoute des points (ex. fidélité match) et enregistre l'action.
     * Débloque les paliers 80 / 100 si atteints.
     */
    public static function addPoints(
        int $createurId,
        string $actionType,
        int $points,
        ?string $sourceType = null,
        ?int $sourceId = null
    ): ?self {
        $annee = (int) now()->format('Y');
        $mois = (int) now()->format('n');
        $scoreFidelite = self::getOrCreateForCreateur($createurId, $annee, $mois);
        $nouveauScore = min(self::SCORE_MAX, $scoreFidelite->score + $points);
        $scoreFidelite->update(['score' => $nouveauScore]);

        ScoreFideliteAction::create([
            'createur_id' => $createurId,
            'annee' => $annee,
            'mois' => $mois,
            'action_type' => $actionType,
            'points' => $points,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);

        self::debloquerPaliersSiAtteints($scoreFidelite->fresh());

        return $scoreFidelite->fresh();
    }

    /** Quand un match est marqué "Acceptée" (créateur est venu). */
    public static function addPointsForMatch(Planning $planning): ?self
    {
        $createurId = $planning->createur_id;
        $dejaCompte = ScoreFideliteAction::where('createur_id', $createurId)
            ->where('source_type', 'planning')
            ->where('source_id', $planning->id)
            ->exists();
        if ($dejaCompte) {
            return null;
        }
        return self::addPoints(
            $createurId,
            self::ACTION_FIDELITE_MATCH,
            self::ACTION_FIDELITE_MATCH_POINTS,
            'planning',
            (int) $planning->id
        );
    }

    public static function debloquerPaliersSiAtteints(self $scoreFidelite): void
    {
        $createur = $scoreFidelite->createur;
        if (! $createur) {
            return;
        }
        $user = $createur->user ?? User::where('email', $createur->email)->first();

        if ($scoreFidelite->score >= self::PALIER_100_POINTS && $scoreFidelite->palier_100_debloque_at === null) {
            $scoreFidelite->update(['palier_100_debloque_at' => now()]);
            $recompense = Recompense::create([
                'createur_id' => $createur->id,
                'type' => null,
                'montant' => self::PALIER_100_MONTANT,
                'raison' => 'Fidélité : palier 100 % (carte cadeau 35 €)',
                'attribue_par' => null,
                'statut' => Recompense::STATUT_EN_ATTENTE_CHOIX,
            ]);
            if ($user) {
                $user->notify(new RecompenseAttribueeNotification($recompense));
            }
        }

        if ($scoreFidelite->score >= self::PALIER_80_POINTS && $scoreFidelite->palier_80_debloque_at === null) {
            $scoreFidelite->update(['palier_80_debloque_at' => now()]);
            $recompense = Recompense::create([
                'createur_id' => $createur->id,
                'type' => null,
                'montant' => self::PALIER_80_MONTANT,
                'raison' => 'Fidélité : palier 80 % (carte cadeau 15 €)',
                'attribue_par' => null,
                'statut' => Recompense::STATUT_EN_ATTENTE_CHOIX,
            ]);
            if ($user) {
                $user->notify(new RecompenseAttribueeNotification($recompense));
            }
        }
    }
}
