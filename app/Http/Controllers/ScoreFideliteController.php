<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\ScoreFidelite;
use App\Models\ScoreFideliteAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoreFideliteController extends Controller
{
    private function anneeMoisCourant(): array
    {
        return [(int) now()->format('Y'), (int) now()->format('n')];
    }

    /**
     * Bonnes actions (score fidélité). Score du mois (remis à 0 le 1er).
     * Créateur = son score + paliers. Staff / Fondateur = liste des créateurs avec score.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        [$annee, $mois] = $this->anneeMoisCourant();
        $moisLabel = now()->translatedFormat('F Y'); // ex. "mars 2026"

        if ($user->isFondateurPrincipal()) {
            $createur = null;
            $createursAvecScore = Createur::query()
                ->orderBy('nom')
                ->get()
                ->map(function (Createur $c) use ($annee, $mois) {
                    $sf = ScoreFidelite::where('createur_id', $c->id)->where('annee', $annee)->where('mois', $mois)->first();
                    return (object) [
                        'createur' => $c,
                        'score' => $sf ? $sf->score : 0,
                        'palier_80' => $sf && $sf->palier_80_debloque_at !== null,
                        'palier_100' => $sf && $sf->palier_100_debloque_at !== null,
                    ];
                })
                ->sortByDesc('score')
                ->values();
            return view('score-fidelite.index', [
                'createur' => null,
                'scoreFidelite' => null,
                'createursAvecScore' => $createursAvecScore,
                'recentActions' => collect(),
                'moisLabel' => $moisLabel,
                'showGestion' => true,
            ]);
        }

        $createur = null;
        $scoreFidelite = null;
        $createursAvecScore = collect();
        $recentActions = collect();
        $showGestion = false;

        if ($user->isCreateur()) {
            $createur = Createur::where('user_id', $user->id)->first()
                ?? Createur::where('email', $user->email)->first();
            if ($createur) {
                $scoreFidelite = ScoreFidelite::getOrCreateForCreateur($createur->id, $annee, $mois);
                // Rattrapage : si le score est déjà >= 80 ou 100 mais le palier n'a pas été débloqué (ex. avant correction)
                ScoreFidelite::debloquerPaliersSiAtteints($scoreFidelite->fresh());
                $scoreFidelite = $scoreFidelite->fresh();
                $recentActions = ScoreFideliteAction::where('createur_id', $createur->id)
                    ->where('annee', $annee)->where('mois', $mois)
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get();
            }
        } else {
            $query = Createur::query()->orderBy('nom');
            if ($user->isAgent()) {
                $query->where('agent_id', $user->id);
            } elseif ($user->scopeToAgenceEquipeId() !== null) {
                $query->where('equipe_id', $user->scopeToAgenceEquipeId());
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $query->where('equipe_id', $user->equipe_id);
            } elseif ($user->isFondateur() || $user->isDirecteur() || $user->isSousDirecteur()) {
                $query->whereNotNull('id'); // tous les créateurs visibles
            }
            foreach ($query->get() as $c) {
                $sf = ScoreFidelite::where('createur_id', $c->id)->where('annee', $annee)->where('mois', $mois)->first();
                $createursAvecScore->push((object) [
                    'createur' => $c,
                    'score' => $sf ? $sf->score : 0,
                    'palier_80' => $sf && $sf->palier_80_debloque_at !== null,
                    'palier_100' => $sf && $sf->palier_100_debloque_at !== null,
                ]);
            }
            $createursAvecScore = $createursAvecScore->sortByDesc('score')->values();
            // Afficher le formulaire « ajouter / retirer des points » pour tous les rôles qui voient des créateurs (agent, manageur, directeur, fondateur).
            $showGestion = $createursAvecScore->isNotEmpty();
        }

        return view('score-fidelite.index', [
            'createur' => $createur,
            'scoreFidelite' => $scoreFidelite,
            'createursAvecScore' => $createursAvecScore,
            'recentActions' => $recentActions,
            'moisLabel' => $moisLabel,
            'showGestion' => $showGestion,
        ]);
    }

    /**
     * Ajouter, retirer des points ou mettre le score à une valeur (mois courant).
     * Autorisé : Fondateur global (tous) ; agents, manageurs, directeurs, fondateurs (créateurs de leur périmètre).
     */
    public function updateScore(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->validate([
            'createur_id' => 'required|exists:createurs,id',
            'action' => 'required|in:add,remove,set',
            'points' => 'required|integer|min:0|max:100',
            'raison' => 'nullable|string|max:255',
        ]);
        $createurId = (int) $request->createur_id;

        // Fondateur global (principal) ou tout fondateur : peut modifier n'importe quel créateur
        if ($user->isFondateurPrincipal() || $user->isFondateur()) {
            // autorisé
        } elseif (! $this->userCanModifyScoreFor($user, $createurId)) {
            abort(403, 'Vous ne pouvez modifier le score que des créateurs de votre périmètre.');
        }
        [$annee, $mois] = $this->anneeMoisCourant();
        $sf = ScoreFidelite::getOrCreateForCreateur($createurId, $annee, $mois);
        $v = (int) $request->points;
        if ($request->action === 'add') {
            $nouveau = min(ScoreFidelite::SCORE_MAX, $sf->score + $v);
        } elseif ($request->action === 'remove') {
            $nouveau = max(0, $sf->score - $v);
        } else {
            $nouveau = max(0, min(ScoreFidelite::SCORE_MAX, $v));
        }
        $sf->update(['score' => $nouveau]);

        $actionType = $request->action === 'add' ? ScoreFidelite::ACTION_MANUAL_ADD
            : ($request->action === 'remove' ? ScoreFidelite::ACTION_MANUAL_REMOVE : ScoreFidelite::ACTION_MANUAL_SET);
        $pointsForAction = $request->action === 'set' ? $nouveau : $v;
        ScoreFideliteAction::create([
            'createur_id' => $createurId,
            'annee' => $annee,
            'mois' => $mois,
            'action_type' => $actionType,
            'points' => $pointsForAction,
            'source_type' => null,
            'source_id' => null,
            'raison' => $request->filled('raison') ? $request->raison : null,
        ]);

        ScoreFidelite::debloquerPaliersSiAtteints($sf->fresh());

        $createur = Createur::find($request->createur_id);
        $msg = $request->action === 'add' ? "{$v} point(s) ajouté(s). Score : {$nouveau}/100."
            : ($request->action === 'remove' ? "{$v} point(s) retiré(s). Score : {$nouveau}/100."
                : "Score mis à {$nouveau}/100.");
        return redirect()->route('score-fidelite.index')->with('success', $createur->nom . ' — ' . $msg);
    }

    /**
     * Vérifie si l'utilisateur peut modifier le score de ce créateur (périmètre agent, équipe, agence, ou tous).
     * Les fondateurs sont gérés avant l'appel (toujours autorisés).
     */
    private function userCanModifyScoreFor($user, int $createurId): bool
    {
        if ($user->isCreateur()) {
            return false;
        }
        $createur = Createur::find($createurId);
        if (! $createur) {
            return false;
        }
        if ($user->isAgent()) {
            return $createur->agent_id === $user->id;
        }
        if ($user->isManageur() || $user->isSousManager()) {
            return $createur->equipe_id === $user->equipe_id;
        }
        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        if ($equipeAgenceId !== null) {
            return $createur->equipe_id === $equipeAgenceId;
        }
        return $user->isDirecteur() || $user->isSousDirecteur();
    }
}
