<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\RapportVendredi;
use App\Models\User;
use Illuminate\Http\Request;

class RapportVendrediController extends Controller
{
    /**
     * Fondateurs (Global + d'Agence) : liste de tous les rapports avec filtres.
     * Autres rôles : mes rapports + formulaire pour la semaine en cours si pas encore rempli.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isCreateur()) {
            abort(403, 'Le rapport de la semaine n\'est pas accessible aux créateurs.');
        }

        if ($user->canVoirTousRapportsVendredi()) {
            return $this->indexFondateur($request);
        }

        return $this->indexMonRapport($request);
    }

    private function indexFondateur(Request $request): \Illuminate\View\View
    {
        $user = $request->user();
        $query = RapportVendredi::with(['user', 'user.equipe', 'validePar'])
            ->orderByDesc('annee')
            ->orderByDesc('semaine')
            ->orderByDesc('created_at');

        // Fondateur d'agence : uniquement les rapports de son équipe
        if ($user->estFondateurSousAgence() && $user->equipe_id) {
            $query->whereHas('user', fn ($q) => $q->where('equipe_id', $user->equipe_id));
        } else {
            $equipeId = $request->filled('equipe_id') ? (int) $request->equipe_id : null;
            if ($equipeId) {
                $query->whereHas('user', fn ($q) => $q->where('equipe_id', $equipeId));
            }
        }

        $userId = $request->filled('user_id') ? (int) $request->user_id : null;
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $annee = $request->filled('annee') ? (int) $request->annee : (int) now()->format('o');
        $semaine = $request->filled('semaine') ? (int) $request->semaine : (int) now()->format('W');
        $query->where('annee', $annee)->where('semaine', $semaine);

        $rapports = $query->get();
        $equipes = Equipe::orderBy('nom')->get();
        $users = User::where('role', '!=', User::ROLE_FONDATEUR)->orderBy('name')->get(['id', 'name', 'equipe_id', 'role']);

        return view('rapport-vendredi.index-fondateur', [
            'rapports' => $rapports,
            'equipes' => $equipes,
            'users' => $users,
            'annee' => $annee,
            'semaine' => $semaine,
        ]);
    }

    private function indexMonRapport(Request $request): \Illuminate\View\View
    {
        $user = $request->user();
        $annee = (int) now()->format('o');
        $semaine = (int) now()->format('W');

        $rapportSemaineCourante = RapportVendredi::where('user_id', $user->id)
            ->where('annee', $annee)
            ->where('semaine', $semaine)
            ->first();

        $mesRapports = RapportVendredi::where('user_id', $user->id)
            ->orderByDesc('annee')
            ->orderByDesc('semaine')
            ->limit(20)
            ->get();

        return view('rapport-vendredi.index', [
            'rapportSemaineCourante' => $rapportSemaineCourante,
            'mesRapports' => $mesRapports,
            'annee' => $annee,
            'semaine' => $semaine,
        ]);
    }

    /**
     * Enregistrer le rapport de la semaine en cours. Réservé aux rôles qui doivent le remplir (tous sauf fondateurs).
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->doitRemplirRapportVendredi()) {
            abort(403, 'Vous n\'êtes pas autorisé à déposer un rapport de la semaine.');
        }

        $annee = (int) now()->format('o');
        $semaine = (int) now()->format('W');

        $request->validate([
            'contenu' => 'required|string|max:10000',
        ]);

        $rapport = RapportVendredi::updateOrCreate(
            [
                'user_id' => $user->id,
                'annee' => $annee,
                'semaine' => $semaine,
            ],
            ['contenu' => $request->contenu]
        );

        return redirect()
            ->route('rapport-vendredi.index')
            ->with('success', 'Rapport de la semaine enregistré. Il permet à la direction de vous accompagner et de vous donner des consignes adaptées.');
    }

    /**
     * Valider un rapport (fondateurs uniquement).
     */
    public function valider(Request $request, RapportVendredi $rapport)
    {
        $user = $request->user();
        if (! $user->canVoirTousRapportsVendredi()) {
            abort(403, 'Seuls les fondateurs peuvent valider les rapports.');
        }
        if ($rapport->isValide()) {
            return redirect()->route('rapport-vendredi.index', $request->only(['annee', 'semaine', 'equipe_id', 'user_id']))
                ->with('info', 'Ce rapport est déjà validé.');
        }
        $rapport->update([
            'valide_at' => now(),
            'valide_par' => $user->id,
        ]);
        return redirect()->route('rapport-vendredi.index', $request->only(['annee', 'semaine', 'equipe_id', 'user_id']))
            ->with('success', 'Rapport validé.');
    }
}
