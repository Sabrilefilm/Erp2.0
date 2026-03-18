<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\ScoreIntegriteHistorique;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoreIntegriteController extends Controller
{
    /** Score actuel pour un créateur (ou global si createur_id null pour anciennes données). */
    private function getScoreActuel(?int $createurId): int
    {
        $query = ScoreIntegriteHistorique::query()->orderByDesc('heure_modification');
        if ($createurId !== null) {
            $query->where('createur_id', $createurId);
        } else {
            $query->whereNull('createur_id');
        }
        $score = $query->value('score_consequent');

        return $score !== null ? (int) $score : ScoreIntegriteHistorique::SCORE_MAX;
    }

    /**
     * Page Score d'intégrité (individuel). Accès : créateur, agent, manageur, directeur — fondateur exclu.
     * Créateur = son score. Agent / Manageur / Directeur = liste des créateurs avec leur score.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->isFondateurPrincipal()) {
            return redirect()->route('score-integrite.gestion');
        }
        if (! $user->canSeeScoreIntegrite()) {
            abort(403, 'Vous n\'avez pas accès à cette page.');
        }

        $createur = null;
        $createursAvecScore = collect();

        if ($user->isCreateur()) {
            $createur = Createur::where('user_id', $user->id)->first()
                ?? Createur::where('email', $user->email)->first();
        }

        if ($createur) {
            $scoreActuel = $this->getScoreActuel($createur->id);
            $historique = ScoreIntegriteHistorique::query()
                ->where('createur_id', $createur->id)
                ->orderByDesc('heure_modification')
                ->limit(50)
                ->get();
        } else {
            $scoreActuel = ScoreIntegriteHistorique::SCORE_MAX;
            $historique = collect();
            $query = Createur::query()->orderBy('nom');
            if ($user->isAgent()) {
                $query->where('agent_id', $user->id);
            } elseif ($user->scopeToAgenceEquipeId() !== null) {
                $query->where('equipe_id', $user->scopeToAgenceEquipeId());
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $query->where('equipe_id', $user->equipe_id);
            }
            foreach ($query->get() as $c) {
                $createursAvecScore->push((object) [
                    'createur' => $c,
                    'score' => $this->getScoreActuel($c->id),
                ]);
            }
        }

        return view('score-integrite.index', [
            'createur' => $createur,
            'scoreActuel' => $scoreActuel,
            'scoreMax' => ScoreIntegriteHistorique::SCORE_MAX,
            'historique' => $historique,
            'createursAvecScore' => $createursAvecScore,
        ]);
    }

    /**
     * Page gestion des infractions et du score (Fondateur uniquement — route protégée par middleware).
     */
    public function gestion(Request $request): View
    {
        // Uniquement les créateurs liés à un compte utilisateur (exclut les fausses personnes)
        $createurs = Createur::query()->whereNotNull('user_id')->orderBy('nom')->get(['id', 'nom', 'pseudo_tiktok']);
        $createurId = $request->get('createur_id');
        $createurSelect = $createurId ? Createur::find($createurId) : null;
        $scoreActuel = $createurSelect ? $this->getScoreActuel($createurSelect->id) : null;
        $historique = ScoreIntegriteHistorique::query()
            ->with('createur')
            ->orderByDesc('heure_modification')
            ->limit(20)
            ->get();

        return view('score-integrite.gestion', [
            'createurs' => $createurs,
            'createurSelect' => $createurSelect,
            'scoreActuel' => $scoreActuel ?? ScoreIntegriteHistorique::SCORE_MAX,
            'scoreMax' => ScoreIntegriteHistorique::SCORE_MAX,
            'historique' => $historique,
        ]);
    }

    /**
     * Enregistrer une infraction et mettre à jour le score (individuel par créateur).
     */
    public function storeInfraction(Request $request): RedirectResponse
    {
        $request->validate([
            'createur_id' => 'required|exists:createurs,id',
            'details_infraction' => 'required|string|max:2000',
            'score_consequent' => 'required|integer|min:0|max:100',
            'sanction_infraction' => 'nullable|string|max:500',
        ]);

        $createurId = (int) $request->createur_id;
        $scoreActuel = $this->getScoreActuel($createurId);
        $scoreConsequent = (int) $request->score_consequent;

        ScoreIntegriteHistorique::create([
            'createur_id' => $createurId,
            'heure_modification' => now(),
            'details_infraction' => $request->details_infraction,
            'score_avant' => $scoreActuel,
            'score_consequent' => $scoreConsequent,
            'sanction_infraction' => $request->filled('sanction_infraction') ? $request->sanction_infraction : null,
        ]);

        $createur = Createur::find($createurId);
        $nom = $createur ? $createur->nom : 'Créateur';

        // Score à 10 % ou moins : blocage automatique de l'accès au compte (créateur uniquement)
        $seuilBlocage = 10;
        if ($createur && $createur->user_id) {
            if ($scoreConsequent <= $seuilBlocage) {
                User::where('id', $createur->user_id)->update(['compte_bloque' => true]);
            } else {
                User::where('id', $createur->user_id)->update(['compte_bloque' => false]);
            }
        }

        $message = $scoreConsequent < $scoreActuel
            ? "Infraction enregistrée pour {$nom}. Score passé de {$scoreActuel} à {$scoreConsequent}."
            : "Score mis à jour pour {$nom}. Nouveau score : {$scoreConsequent}/100.";
        if ($scoreConsequent <= $seuilBlocage && $createur && $createur->user_id) {
            $message .= ' L\'accès au compte de ce créateur a été bloqué automatiquement.';
        }

        return redirect()->route('score-integrite.gestion', ['createur_id' => $createurId])->with('success', $message);
    }
}
