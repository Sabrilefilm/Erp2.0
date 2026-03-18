<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->endOfMonth()->addMonths(1)->format('Y-m-d'));

        if (! Schema::hasTable('planning')) {
            $planning = collect();
        } else {
            $query = Planning::with(['createur.equipe', 'creePar']);

            $equipeAgenceId = $user->scopeToAgenceEquipeId();
            if ($equipeAgenceId !== null) {
                $query->whereHas('createur', fn ($q) => $q->where('equipe_id', $equipeAgenceId));
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $query->whereHas('createur', fn ($q) => $q->where('equipe_id', $user->equipe_id));
            } elseif ($user->isAgent()) {
                $query->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id));
            } elseif ($user->isAmbassadeur()) {
                $query->whereHas('createur', fn ($q) => $q->where('ambassadeur_id', $user->id));
            } elseif ($user->isCreateur()) {
                $query->whereHas('createur', fn ($q) => $q->where('email', $user->email));
            }

            $query->whereBetween('date', [$from, $to]);
            $planning = $query->orderBy('date')->orderBy('createur_id')->get();
        }

        $planningByDate = $planning->groupBy(fn ($p) => $p->date->format('Y-m-d'));

        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        $createurs = Createur::query()
            ->when($equipeAgenceId !== null, fn ($q) => $q->where('equipe_id', $equipeAgenceId))
            ->when($equipeAgenceId === null && ($user->isManageur() || $user->isSousManager()), fn ($q) => $q->where('equipe_id', $user->equipe_id))
            ->when($user->isAgent(), fn ($q) => $q->where('agent_id', $user->id))
            ->when($user->isAmbassadeur(), fn ($q) => $q->where('ambassadeur_id', $user->id))
            ->when($user->isCreateur(), fn ($q) => $q->where('email', $user->email))
            ->when($equipeAgenceId === null && ($user->isSousDirecteur() || $user->isDirecteur() || $user->isFondateurPrincipal()), fn ($q) => $q)
            ->orderBy('nom')
            ->get(['id', 'nom', 'pseudo_tiktok']);

        return view('planning.index', compact('planning', 'planningByDate', 'createurs', 'from', 'to'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'createur_id' => 'required|exists:createurs,id',
            'date' => 'required|date',
            'heure' => 'nullable|date_format:H:i',
            'type' => 'required|string|in:match_off,match_anniversaire,match_depannage,match_tournoi,match_agence',
            'raison' => 'nullable|string|max:255',
            'createur_adverse' => 'nullable|string|max:255',
        ]);

        $exists = Planning::where('createur_id', $request->createur_id)
            ->where('date', $request->date)
            ->where('type', $request->type)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Un événement existe déjà pour ce créateur à cette date.');
        }

        Planning::create([
            'createur_id' => $request->createur_id,
            'date' => $request->date,
            'heure' => $request->heure,
            'type' => $request->type,
            'raison' => $request->raison,
            'createur_adverse' => $request->createur_adverse,
            'cree_par' => $request->user()->id,
        ]);

        return back()->with('success', 'Événement ajouté.');
    }

    public function destroy(Planning $planning)
    {
        $planning->load('createur');
        $user = auth()->user();
        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        if ($equipeAgenceId !== null && $planning->createur->equipe_id !== $equipeAgenceId) {
            abort(403);
        }
        if (($user->isManageur() || $user->isSousManager()) && $planning->createur->equipe_id !== $user->equipe_id) {
            abort(403);
        }
        if ($user->isAgent() && $planning->createur->agent_id !== $user->id) {
            abort(403);
        }
        $planning->delete();
        return back()->with('success', 'Événement supprimé.');
    }
}
