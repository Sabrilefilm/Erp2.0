<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\Sanction;
use App\Models\User;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Sanction::with(['createur.equipe', 'attribuePar'])->latest();

        if ($user->isManageur() || $user->isSousManager()) {
            $query->whereHas('createur', fn ($q) => $q->where('equipe_id', $user->equipe_id));
        } elseif ($user->isAgent()) {
            $query->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id));
        } elseif ($user->isCreateur()) {
            $query->whereHas('createur', fn ($q) => $q->where('email', $user->email));
        }

        $sanctions = $query->paginate(20)->withQueryString();

        // Liste : uniquement les créateurs liés à un compte utilisateur (exclut les fausses personnes)
        $createurs = Createur::query()
            ->whereNotNull('user_id')
            ->when($user->isManageur() || $user->isSousManager(), fn ($q) => $q->where('equipe_id', $user->equipe_id))
            ->when($user->isAgent(), fn ($q) => $q->where('agent_id', $user->id))
            ->when($user->isCreateur(), fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('nom')
            ->get(['id', 'nom', 'pseudo_tiktok']);

        return view('sanctions.index', compact('sanctions', 'createurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'createur_id' => 'required|exists:createurs,id',
            'type' => 'required|string|in:Avertissement,Blâme,Suspension,Exclusion',
            'niveau' => 'nullable|string|in:agent,agence',
            'raison' => 'nullable|string|max:1000',
        ]);

        Sanction::create([
            'createur_id' => $request->createur_id,
            'type' => $request->type,
            'niveau' => $request->get('niveau', 'agence'),
            'raison' => $request->raison,
            'attribue_par' => $request->user()->id,
            'statut' => 'actif',
        ]);

        return back()->with('success', 'Sanction enregistrée.');
    }
}
