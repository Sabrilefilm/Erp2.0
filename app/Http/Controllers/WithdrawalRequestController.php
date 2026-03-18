<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->canAddEntries()) {
            abort(403, 'Seuls les fondateurs, directeurs et manageurs peuvent accéder aux demandes de retrait.');
        }

        $query = WithdrawalRequest::with(['createur.equipe', 'traitePar'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(15)->withQueryString();

        // Uniquement les créateurs liés à un compte utilisateur (exclut les fausses personnes)
        $createurs = Createur::query()
            ->whereNotNull('user_id')
            ->orderBy('nom')
            ->get(['id', 'nom', 'pseudo_tiktok']);

        return view('withdrawals.index', compact('withdrawals', 'createurs'));
    }

    public function store(Request $request)
    {
        if (! $request->user()->canAddEntries()) {
            abort(403, 'Seuls les fondateurs, directeurs et manageurs peuvent créer une demande de retrait.');
        }
        $request->validate([
            'createur_id' => 'required|exists:createurs,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|string|in:virement,carte-cadeau,tiktok-live',
            'notes' => 'nullable|string|max:1000',
        ]);

        WithdrawalRequest::create([
            'createur_id' => $request->createur_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Demande de retrait enregistrée.');
    }

    public function update(Request $request, WithdrawalRequest $withdrawal_request)
    {
        if (! $request->user()->canAddEntries()) {
            abort(403, 'Seuls les fondateurs, directeurs et manageurs peuvent approuver ou refuser une demande de retrait.');
        }

        $request->validate(['status' => 'required|in:approved,rejected']);

        $withdrawal_request->update([
            'status' => $request->status,
            'traite_par' => $request->user()->id,
            'traite_at' => now(),
        ]);

        $label = $request->status === 'approved' ? 'approuvée' : 'refusée';
        return back()->with('success', "Demande {$label}.");
    }
}
