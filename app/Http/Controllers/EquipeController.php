<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * CRUD Agences (équipes) — réservé au Fondateur Global (is_fondateur_principal).
 * Protégé par le middleware fondateur.only dans routes/web.php.
 */
class EquipeController extends Controller
{
    /**
     * Page dédiée « Attribution agences » : liste des créateurs (et autres rôles) avec attribution à une agence ou sous-agence.
     */
    public function attribution()
    {
        $equipes = Equipe::orderBy('nom')->get(['id', 'nom']);

        $createurs = User::with(['equipe', 'createur', 'createur.equipe'])
            ->where('role', User::ROLE_CREATEUR)
            ->whereHas('createur')
            ->orderBy('name')
            ->get();

        $autresMembres = User::with('equipe')
            ->whereIn('role', [
                User::ROLE_DIRECTEUR,
                User::ROLE_SOUS_DIRECTEUR,
                User::ROLE_MANAGEUR,
                User::ROLE_SOUS_MANAGER,
                User::ROLE_AGENT,
                User::ROLE_AMBASSADEUR,
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'role', 'equipe_id']);

        return view('equipes.attribution', compact('equipes', 'createurs', 'autresMembres'));
    }

    /**
     * Mise à jour de l'agence d'un utilisateur depuis la page Attribution agences.
     */
    public function assignerAgenceFromAttribution(Request $request, User $user)
    {
        $request->validate(['equipe_id' => 'nullable|exists:equipes,id']);

        $equipeId = $request->filled('equipe_id') ? (int) $request->equipe_id : null;
        $user->update(['equipe_id' => $equipeId]);

        if ($user->createur) {
            $user->createur->update(['equipe_id' => $equipeId]);
        }

        return redirect()->route('equipes.attribution')->with('success', 'Agence de ' . $user->name . ' mise à jour.');
    }

    public function index()
    {
        $equipes = Equipe::withCount('membres')
            ->withCount('createurs')
            ->with('manager')
            ->orderBy('nom')
            ->get();

        return view('equipes.index', compact('equipes'));
    }

    public function create()
    {
        $agentsEtManagers = User::whereIn('role', [
            User::ROLE_DIRECTEUR,
            User::ROLE_SOUS_DIRECTEUR,
            User::ROLE_MANAGEUR,
            User::ROLE_SOUS_MANAGER,
            User::ROLE_AGENT,
            User::ROLE_AMBASSADEUR,
        ])->orderBy('name')->get(['id', 'name', 'role']);

        return view('equipes.create', compact('agentsEtManagers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'est_partenaire' => 'nullable|boolean',
        ]);

        Equipe::create([
            'nom' => $request->nom,
            'manager_id' => $request->manager_id ?: null,
            'est_partenaire' => $request->boolean('est_partenaire'),
        ]);

        return redirect()->route('equipes.index')->with('success', 'Agence créée.');
    }

    public function edit(Equipe $equipe)
    {
        $agentsEtManagers = User::whereIn('role', [
            User::ROLE_DIRECTEUR,
            User::ROLE_SOUS_DIRECTEUR,
            User::ROLE_MANAGEUR,
            User::ROLE_SOUS_MANAGER,
            User::ROLE_AGENT,
            User::ROLE_AMBASSADEUR,
        ])->orderBy('name')->get(['id', 'name', 'role']);

        return view('equipes.edit', compact('equipe', 'agentsEtManagers'));
    }

    public function update(Request $request, Equipe $equipe)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'est_partenaire' => 'nullable|boolean',
        ]);

        $equipe->update([
            'nom' => $request->nom,
            'manager_id' => $request->manager_id ?: null,
            'est_partenaire' => $request->boolean('est_partenaire'),
        ]);

        return redirect()->route('equipes.index')->with('success', 'Agence mise à jour.');
    }

    public function destroy(Equipe $equipe)
    {
        $membres = $equipe->membres()->count();
        $createurs = $equipe->createurs()->count();
        if ($membres > 0 || $createurs > 0) {
            return redirect()->route('equipes.index')
                ->with('error', 'Impossible de supprimer cette agence : elle contient encore des membres ou des créateurs. Réassignez-les à une autre agence.');
        }

        $equipe->delete();
        return redirect()->route('equipes.index')->with('success', 'Agence supprimée.');
    }

    /**
     * Page « Attribuer les membres » : liste des personnes de cette agence + formulaire pour en ajouter ou modifier agence/rôle.
     */
    public function membres(Equipe $equipe)
    {
        $membres = User::with(['equipe', 'createur', 'createur.agent'])
            ->where('equipe_id', $equipe->id)
            ->orderBy('name')
            ->get();

        $createursSansUser = Createur::where('equipe_id', $equipe->id)->whereNull('user_id')->with('agent')->orderBy('nom')->get();

        $usersHorsAgence = User::with('equipe')
            ->where(function ($q) use ($equipe) {
                $q->whereNull('equipe_id')->orWhere('equipe_id', '!=', $equipe->id);
            })
            ->where('id', '!=', request()->user()->id)
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'role', 'equipe_id']);

        $autresEquipes = Equipe::where('id', '!=', $equipe->id)->orderBy('nom')->get(['id', 'nom']);

        return view('equipes.membres', compact('equipe', 'membres', 'createursSansUser', 'usersHorsAgence', 'autresEquipes'));
    }

    /**
     * Attribuer un utilisateur à cette agence (Fondateur Global).
     */
    public function attribuer(Request $request, Equipe $equipe)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = User::findOrFail($request->user_id);
        $user->update(['equipe_id' => $equipe->id]);

        if ($user->createur) {
            $user->createur->update(['equipe_id' => $equipe->id]);
        }

        return redirect()->route('equipes.membres', $equipe)->with('success', $user->name . ' a été attribué à cette agence.');
    }

    /**
     * Changer l'agence d'un utilisateur (depuis la page membres d'une agence).
     */
    public function changerAgence(Request $request, Equipe $equipe, User $user)
    {
        if ($user->equipe_id !== (int) $equipe->id) {
            return redirect()->route('equipes.membres', $equipe)->with('error', 'Cet utilisateur n’appartient pas à cette agence.');
        }

        $request->validate(['nouvelle_equipe_id' => 'nullable|exists:equipes,id']);

        $nouvelleEquipeId = $request->filled('nouvelle_equipe_id') ? (int) $request->nouvelle_equipe_id : null;
        $user->update(['equipe_id' => $nouvelleEquipeId]);

        if ($user->createur) {
            $user->createur->update(['equipe_id' => $nouvelleEquipeId]);
        }

        $libelle = $nouvelleEquipeId ? Equipe::find($nouvelleEquipeId)->nom : 'Aucune agence';
        return redirect()->route('equipes.membres', $equipe)->with('success', 'Agence de ' . $user->name . ' mise à jour : ' . $libelle . '.');
    }
}
