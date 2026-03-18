<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = User::with(['equipe', 'equipe.agents', 'equipe.manager', 'manager', 'createur', 'createur.agent', 'createur.equipe', 'createur.equipe.manager']);

        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        if ($user->isFondateurPrincipal() && $equipeAgenceId === null) {
            // Tous (vue générale) — les fondateurs ne figurent pas dans la liste
        } elseif ($equipeAgenceId !== null) {
            $query->where(function ($q) use ($equipeAgenceId) {
                $q->where('equipe_id', $equipeAgenceId)
                    ->orWhereHas('createur', fn ($q2) => $q2->where('equipe_id', $equipeAgenceId));
            });
        } elseif ($user->isDirecteur() || $user->isSousDirecteur()) {
            $query->where('role', '!=', User::ROLE_FONDATEUR);
        } elseif ($user->isManageur() || $user->isSousManager()) {
            $eqId = $user->equipe_id;
            $query->where(function ($q) use ($eqId) {
                $q->where('equipe_id', $eqId)
                    ->orWhereHas('createur', fn ($q2) => $q2->where('equipe_id', $eqId));
            });
        } elseif ($user->isAgent() || $user->isAmbassadeur()) {
            $eqId = $user->equipe_id;
            $query->where(function ($q) use ($eqId) {
                $q->where('equipe_id', $eqId)
                    ->orWhereHas('createur', fn ($q2) => $q2->where('equipe_id', $eqId));
            });
        } else {
            $query->where('id', $user->id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $term = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('username', 'like', $term);
            });
        }

        $users = $query->orderByRaw('LOWER(name) ASC')->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Fragment HTML pour mise à jour en temps réel (polling).
     */
    public function tableFragment(Request $request)
    {
        $user = $request->user();
        $query = User::with(['equipe', 'equipe.agents', 'equipe.manager', 'manager', 'createur', 'createur.agent', 'createur.equipe', 'createur.equipe.manager']);

        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        if ($user->isFondateurPrincipal() && $equipeAgenceId === null) {
            // Tous
        } elseif ($equipeAgenceId !== null) {
            $query->where(function ($q) use ($equipeAgenceId) {
                $q->where('equipe_id', $equipeAgenceId)
                    ->orWhereHas('createur', fn ($q2) => $q2->where('equipe_id', $equipeAgenceId));
            });
        } elseif ($user->isDirecteur() || $user->isSousDirecteur()) {
            $query->where('role', '!=', User::ROLE_FONDATEUR);
        } elseif ($user->isManageur() || $user->isSousManager()) {
            $eqId = $user->equipe_id;
            $query->where(function ($q) use ($eqId) {
                $q->where('equipe_id', $eqId)
                    ->orWhereHas('createur', fn ($q2) => $q2->where('equipe_id', $eqId));
            });
        } elseif ($user->isAgent() || $user->isAmbassadeur()) {
            $eqId = $user->equipe_id;
            $query->where(function ($q) use ($eqId) {
                $q->where('equipe_id', $eqId)
                    ->orWhereHas('createur', fn ($q2) => $q2->where('equipe_id', $eqId));
            });
        } else {
            $query->where('id', $user->id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $term = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('username', 'like', $term);
            });
        }

        $users = $query->orderByRaw('LOWER(name) ASC')->orderBy('name')->paginate(15)->withQueryString()->appends($request->only('page', 'role', 'q'));

        return response()
            ->view('users.partials.table-content', compact('users'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function create(Request $request)
    {
        Gate::authorize('create', User::class);

        $agents = User::with('equipe')->whereIn('role', [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR])->orderBy('name')->get();
        $managers = User::whereIn('role', [User::ROLE_DIRECTEUR, User::ROLE_SOUS_DIRECTEUR, User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER])
            ->where('id', '!=', $request->user()->id)->orderBy('name')->get();
        $allowedRoles = $this->getAllowedRolesFor($request->user());
        $equipes = $request->user()->isFondateurPrincipal()
            ? Equipe::orderBy('nom')->get(['id', 'nom'])
            : collect();

        return view('users.create', compact('agents', 'managers', 'allowedRoles', 'equipes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $allowedRoles = $this->getAllowedRolesFor($request->user());

        $rules = [
            'username' => 'required|string|max:255|unique:users,username',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:'.implode(',', $allowedRoles),
            'agent_id' => 'nullable|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
        ];
        if ($request->user()->isFondateurPrincipal()) {
            $rules['equipe_id'] = 'nullable|exists:equipes,id';
        }
        $request->validate($rules);

        $currentUser = $request->user();
        $equipeId = null;
        $agentId = null;
        if ($currentUser->isFondateurPrincipal() && $request->filled('equipe_id')) {
            $equipeId = (int) $request->equipe_id;
        }
        if ($equipeId === null && $request->role === User::ROLE_CREATEUR && $request->filled('agent_id')) {
            $agent = User::find($request->agent_id);
            if ($agent) {
                $agentId = $agent->id;
                $equipeId = $agent->equipe_id;
            }
        }
        if ($currentUser->estFondateurSousAgence() && in_array($request->role, [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_CREATEUR], true)) {
            $equipeId = $equipeId ?? $currentUser->equipe_id;
        }

        $newUser = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'email' => null,
            'phone' => $request->filled('phone') ? $request->phone : null,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'equipe_id' => $request->role === User::ROLE_CREATEUR ? $equipeId : ($currentUser->estFondateurSousAgence() ? $equipeId : ($currentUser->isFondateurPrincipal() ? $equipeId : null)),
            'manager_id' => $request->manager_id,
        ]);

        if ($newUser->isCreateur()) {
            Createur::firstOrCreate(
                ['user_id' => $newUser->id],
                ['nom' => $newUser->name, 'email' => $newUser->email, 'equipe_id' => $equipeId, 'agent_id' => $agentId]
            );
        }

        $message = $request->role === User::ROLE_CREATEUR ? 'Créateur ajouté.' : 'Utilisateur créé.';
        return redirect()->route('users.index')->with('success', $message);
    }

    public function edit(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $agents = User::with('equipe')->whereIn('role', [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR])->orderBy('name')->get();
        $managers = User::whereIn('role', [User::ROLE_DIRECTEUR, User::ROLE_SOUS_DIRECTEUR, User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER])
            ->where('id', '!=', $user->id)->orderBy('name')->get();
        $allowedRoles = $this->getAllowedRolesFor($request->user());
        $equipes = $request->user()->isFondateurPrincipal()
            ? Equipe::orderBy('nom')->get(['id', 'nom'])
            : collect();
        // Inclure le rôle actuel de l'utilisateur édité pour qu'il apparaisse dans le select (ex. Fondateur)
        if (! in_array($user->role, $allowedRoles)) {
            $allowedRoles = array_merge([$user->role], $allowedRoles);
        }

        return view('users.edit', compact('user', 'agents', 'managers', 'allowedRoles', 'equipes'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $allowedRoles = $this->getAllowedRolesFor($request->user());
        // Autoriser aussi le rôle actuel (ex. fondateur) pour ne pas bloquer l'enregistrement sans changement
        $allowedRolesForValidation = array_values(array_unique(array_merge($allowedRoles, [$user->role])));

        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'role' => 'required|in:'.implode(',', $allowedRolesForValidation),
            'agent_id' => 'nullable|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
        ];
        if ($request->user()->isFondateurPrincipal()) {
            $rules['equipe_id'] = 'nullable|exists:equipes,id';
        }
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }
        $request->validate($rules);

        $data = $request->only(['name', 'username', 'email', 'phone', 'date_naissance', 'role', 'manager_id']);
        if (array_key_exists('date_naissance', $data)) {
            $data['date_naissance'] = $request->filled('date_naissance') ? $request->date_naissance : null;
        }
        $data['equipe_id'] = null;
        if ($request->user()->isFondateurPrincipal() && $request->has('equipe_id')) {
            $data['equipe_id'] = $request->filled('equipe_id') ? (int) $request->equipe_id : null;
        } elseif ($request->role === User::ROLE_CREATEUR && $request->filled('agent_id')) {
            $agent = User::find($request->agent_id);
            if ($agent) {
                $data['equipe_id'] = $agent->equipe_id;
            }
        }
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->boolean('clear_login_block')) {
            $data['login_blocked_until'] = null;
            $data['must_change_password'] = false;
        }
        $user->update($data);

        if ($user->isCreateur()) {
            $createurFiche = Createur::firstOrCreate(
                ['user_id' => $user->id],
                ['nom' => $user->name, 'email' => $user->email]
            );
            $agentId = $request->filled('agent_id') ? $request->agent_id : null;
            $eqId = $user->equipe_id;
            $createurFiche->update(['equipe_id' => $eqId, 'agent_id' => $agentId]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        if ($user->createur) {
            $user->createur->delete();
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }

    /** Mon profil : formulaire pour l'utilisateur connecté (tous les rôles). */
    public function editProfile(Request $request)
    {
        $user = $request->user();
        $agents = User::with('equipe')->whereIn('role', [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR])->orderBy('name')->get();
        $managers = User::whereIn('role', [User::ROLE_DIRECTEUR, User::ROLE_SOUS_DIRECTEUR, User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER])
            ->where('id', '!=', $user->id)->orderBy('name')->get();
        $allowedRoles = [$user->role];

        return view('users.edit', [
            'user' => $user,
            'agents' => $agents,
            'managers' => $managers,
            'allowedRoles' => $allowedRoles,
            'isProfile' => true,
        ]);
    }

    /** Mise à jour du profil (nom, username, phone, password uniquement). Les agents ne peuvent pas modifier leur mot de passe. */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'phone' => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
        ];
        if (! $user->isAgent()) {
            $rules['password'] = ['nullable', 'confirmed', Password::defaults()];
        }
        $request->validate($rules);
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->filled('phone') ? $request->phone : null,
            'date_naissance' => $request->filled('date_naissance') ? $request->date_naissance : null,
        ];
        if ($request->filled('password') && ! $user->isAgent()) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        return redirect()->route('dashboard')->with('success', 'Profil mis à jour.');
    }

    /** Rôles que l'utilisateur connecté peut attribuer (selon sa position dans la hiérarchie). */
    private function getAllowedRolesFor(User $user): array
    {
        if ($user->isFondateurPrincipal()) {
            return [User::ROLE_FONDATEUR, User::ROLE_DIRECTEUR, User::ROLE_SOUS_DIRECTEUR, User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_CREATEUR];
        }
        if ($user->estFondateurSousAgence()) {
            return [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_CREATEUR];
        }
        if ($user->isDirecteur()) {
            return [User::ROLE_SOUS_DIRECTEUR, User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_CREATEUR];
        }
        if ($user->isSousDirecteur()) {
            return [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_CREATEUR];
        }
        if ($user->isManageur()) {
            return [User::ROLE_SOUS_MANAGER, User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_CREATEUR];
        }
        if ($user->isSousManager()) {
            return [User::ROLE_AGENT, User::ROLE_AMBASSADEUR, User::ROLE_CREATEUR];
        }
        return [];
    }
}
