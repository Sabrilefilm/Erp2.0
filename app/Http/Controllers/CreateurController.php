<?php

namespace App\Http\Controllers;

use App\Models\CommentaireInterne;
use App\Models\Createur;
use App\Models\CreateurStatMensuelle;
use App\Models\DemandeMatch;
use App\Models\Equipe;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CreateurController extends Controller
{
    /**
     * Liste des créateurs (staff uniquement). Le rôle créateur n'a pas cette page (comme ERP V1 : il a "Ma Fiche" = dashboard).
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();
        if ($currentUser->isCreateur()) {
            $createurRecord = Createur::where('user_id', $currentUser->id)->first()
                ?? Createur::where('email', $currentUser->email)->first();
            if ($createurRecord) {
                return redirect()->route('createurs.show', $createurRecord);
            }
            return redirect()->route('dashboard');
        }

        // Tous les utilisateurs avec le rôle créateur (même sans fiche), pour avoir la même liste qu’en Utilisateurs
        $query = User::with(['equipe', 'equipe.agents', 'manager', 'createur', 'createur.agent'])
            ->where('role', User::ROLE_CREATEUR);

        $equipes = collect();
        $equipeFilter = null;
        $showEquipeFilter = false;

        $equipeAgenceId = $currentUser->scopeToAgenceEquipeId();
        if ($currentUser->isFondateurPrincipal() && $equipeAgenceId === null) {
            $equipes = Equipe::orderBy('nom')->get();
            $showEquipeFilter = $equipes->isNotEmpty();
            if ($request->filled('equipe_id')) {
                $query->where('equipe_id', $request->equipe_id);
                $equipeFilter = Equipe::find($request->equipe_id);
            }
        } elseif ($equipeAgenceId !== null) {
            $query->where('equipe_id', $equipeAgenceId);
            $equipeFilter = Equipe::find($equipeAgenceId);
        } elseif ($currentUser->isDirecteur() || $currentUser->isSousDirecteur()) {
            $query->where('role', '!=', User::ROLE_FONDATEUR);
            $equipes = Equipe::orderBy('nom')->get();
            $showEquipeFilter = $equipes->isNotEmpty();
            if ($request->filled('equipe_id')) {
                $query->where('equipe_id', $request->equipe_id);
                $equipeFilter = Equipe::find($request->equipe_id);
            }
        } elseif ($currentUser->isManageur() || $currentUser->isSousManager()) {
            $query->where('equipe_id', $currentUser->equipe_id);
            $equipeFilter = $currentUser->equipe;
        } elseif ($currentUser->isAgent()) {
            $query->where('equipe_id', $currentUser->equipe_id);
            $equipeFilter = $currentUser->equipe;
        } elseif ($currentUser->isAmbassadeur()) {
            $query->where('equipe_id', $currentUser->equipe_id);
            $equipeFilter = $currentUser->equipe;
        } else {
            $query->where('id', $currentUser->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('username', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $createurs = $query->orderBy('name')->paginate(15)->withQueryString();
        $totalCreateurs = (clone $query)->count();

        return view('createurs.index', compact('createurs', 'totalCreateurs', 'equipes', 'equipeFilter', 'showEquipeFilter'));
    }

    /**
     * Page réservée aux agents : liste de leurs créateurs (ceux dont ils sont l'agent)
     * avec Heures, Jours, Diamants et nombre de demandes de match en attente.
     */
    public function mesCreateurs(Request $request)
    {
        $user = $request->user();
        if (!$user->isAgent()) {
            abort(403, 'Cette page est réservée aux agents.');
        }

        // Tous les créateurs dont tu es l'agent (avec ou sans compte utilisateur)
        $createurs = Createur::where('agent_id', $user->id)
            ->with(['user', 'equipe'])
            ->withCount(['demandesMatch as demandes_en_attente' => fn ($q) => $q->where('statut', DemandeMatch::STATUT_EN_ATTENTE)])
            ->orderBy('nom')
            ->get();

        return view('createurs.mes-createurs', compact('createurs'));
    }

    public function show(Request $request, Createur $createur)
    {
        Gate::authorize('view', $createur);

        $createur->load(['equipe', 'agent', 'ambassadeur', 'statsMensuelles']);
        $commentaires = CommentaireInterne::where('createur_id', $createur->id)->with('user')->latest()->get();

        $moisDisponibles = collect();
        foreach ($createur->statsMensuelles as $s) {
            $moisDisponibles->push(['annee' => $s->annee, 'mois' => $s->mois, 'libelle' => $s->libelle]);
        }
        $hasCurrent = $createur->heures_mois !== null || $createur->jours_mois !== null || $createur->diamants !== null;
        $nowY = (int) now()->format('Y');
        $nowM = (int) now()->format('n');
        $noms = [1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        if ($hasCurrent && $moisDisponibles->where('annee', $nowY)->where('mois', $nowM)->isEmpty()) {
            $moisDisponibles->prepend(['annee' => $nowY, 'mois' => $nowM, 'libelle' => ($noms[$nowM] ?? '').' '.$nowY]);
        }
        $moisDisponibles = $moisDisponibles->sortByDesc('annee')->sortByDesc('mois')->values();

        $first = $moisDisponibles->first();
        $defAnnee = $first ? (int) $first['annee'] : (int) now()->format('Y');
        $defMois = $first ? (int) $first['mois'] : (int) now()->format('n');
        $annee = (int) $request->input('annee', $defAnnee);
        $mois = (int) $request->input('mois', $defMois);
        $valid = $moisDisponibles->contains(fn ($m) => (int) $m['annee'] === $annee && (int) $m['mois'] === $mois);
        if (! $valid) {
            $annee = $defAnnee;
            $mois = $defMois;
        }

        $statMensuelle = $createur->statsMensuelles->firstWhere(fn ($s) => (int) $s->annee === $annee && (int) $s->mois === $mois);
        if (! $statMensuelle) {
            $statMensuelle = CreateurStatMensuelle::where('createur_id', $createur->id)
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->first();
        }
        if (! $statMensuelle && $annee === $nowY && $mois === $nowM) {
            $jours = $createur->jours_mois;
            $heures = $createur->heures_mois;
            $diamants = $createur->diamants;
        } else {
            $jours = $statMensuelle?->jours_stream;
            $heures = $statMensuelle?->heures_stream;
            $diamants = $statMensuelle?->diamants;
        }

        $agents = User::whereIn('role', [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR])->orderBy('name')->get();
        $equipes = Equipe::orderBy('nom')->get();
        $ambassadeurs = User::where('role', User::ROLE_AMBASSADEUR)->orderBy('name')->get();

        return view('createurs.show', compact('createur', 'commentaires', 'moisDisponibles', 'annee', 'mois', 'jours', 'heures', 'diamants', 'agents', 'equipes', 'ambassadeurs'));
    }

    /**
     * Télécharger le contrat de prestation du créateur en PDF (1 page).
     */
    public function contratPdf(Createur $createur)
    {
        Gate::authorize('view', $createur);

        $createur->load('user');
        $nomTiktok = $createur->pseudo_tiktok
            ? ltrim($createur->pseudo_tiktok, '@')
            : ($createur->user?->username ?? '____________________________');
        $email = $createur->email ?? $createur->user?->email ?? '____________________________';
        $telephone = $createur->user?->phone ?? '____________________________';
        $dateSignature = now()->format('d / m / Y');

        $pdf = Pdf::loadView('createurs.contrat-pdf', [
            'createur' => $createur,
            'nomTiktok' => $nomTiktok,
            'email' => $email,
            'telephone' => $telephone,
            'dateSignature' => $dateSignature,
            'signedAt' => $createur->contrat_signe_le?->format('d/m/Y à H:i'),
        ])->setPaper('a4', 'portrait');

        $filename = 'contrat-prestation-' . preg_replace('/[^a-zA-Z0-9\-_]/', '-', $createur->nom ?? 'createur') . '-' . $createur->id . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Mise à jour de l'attribution : agent, équipe, ambassadeur.
     * Synchronise aussi User.equipe_id si le créateur a un user_id.
     */
    public function updateAttribution(Request $request, Createur $createur)
    {
        Gate::authorize('update', $createur);

        $request->validate([
            'agent_id' => 'nullable|exists:users,id',
            'equipe_id' => 'nullable|exists:equipes,id',
            'ambassadeur_id' => 'nullable|exists:users,id',
        ]);

        $agent = $request->agent_id ? User::find($request->agent_id) : null;
        $createur->update([
            'agent_id' => $request->agent_id,
            'equipe_id' => $agent?->equipe_id ?? $request->equipe_id,
            'ambassadeur_id' => $request->ambassadeur_id,
        ]);

        if ($createur->user_id) {
            User::where('id', $createur->user_id)->update([
                'equipe_id' => $createur->equipe_id,
            ]);
        }

        return back()->with('success', 'Attribution mise à jour.');
    }

    /**
     * Mise à jour des notes et du statut uniquement.
     * Règle centrale : Jours / Heures / Diamants ne sont jamais modifiables ici — uniquement par le fondateur via l'import Excel.
     */
    public function updateNotes(Request $request, Createur $createur)
    {
        Gate::authorize('update', $createur);

        $request->validate([
            'notes' => 'nullable|string|max:5000',
            'statut' => 'nullable|string|max:64',
        ]);

        // Seuls notes et statut : jamais heures_mois, jours_mois, diamants (réservés à l'import fondateur)
        $createur->update($request->only(['notes', 'statut']));

        return back()->with('success', 'Mis à jour.');
    }

    public function storeCommentaire(Request $request, Createur $createur)
    {
        $user = $request->user();
        if (! $user->isFondateur() && ! $user->isDirecteur() && ! $user->isSousDirecteur() && ! $user->isManageur() && ! $user->isSousManager() && ! $user->isAgent()) {
            abort(403, 'Seuls fondateur, directeur, sous-directeur, manageur, sous-manager et agent peuvent ajouter des commentaires.');
        }

        Gate::authorize('view', $createur);

        $request->validate(['contenu' => 'required|string|max:2000']);

        CommentaireInterne::create([
            'createur_id' => $createur->id,
            'user_id' => $user->id,
            'contenu' => $request->contenu,
        ]);

        return back()->with('success', 'Commentaire ajouté.');
    }

    /**
     * Supprimer définitivement un créateur (fiche + historique score, sanctions, etc.).
     * Réservé au fondateur. À utiliser pour enlever les personnes qui ne font pas partie de l'agence.
     */
    public function destroy(Createur $createur)
    {
        Gate::authorize('delete', $createur);

        $nom = $createur->nom;
        $createur->delete();

        return redirect()->route('score-integrite.gestion')->with('success', "Créateur « {$nom} » supprimé définitivement.");
    }
}
