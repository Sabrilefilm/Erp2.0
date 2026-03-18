@extends('layouts.app')

@section('title', 'Retraits')

@section('content')
<div class="space-y-6 pb-8 max-w-4xl mx-auto">
    {{-- Hero type app --}}
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e3a8a] to-[#1e40af] border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-tight">Demandes de retrait</h1>
                <p class="text-blue-200/90 text-sm mt-1">Virements et paiements créateurs</p>
            </div>
        </div>
    </div>

    @if(auth()->user()->canAddEntries())
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">➕ Nouvelle demande</h2>
        <form action="{{ route('withdrawals.store') }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">
                    Créateur <a href="{{ route('withdrawals.index', request()->only('status')) }}" class="ml-1 text-sky-400 hover:underline text-xs">Rafraîchir</a>
                </label>
                <select name="createur_id" required class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm min-w-[200px] focus:outline-none focus:border-sky-500/50">
                    <option value="">— Choisir un créateur —</option>
                    @forelse($createurs as $c)
                    <option value="{{ $c->id }}">{{ $c->nom }}{{ $c->pseudo_tiktok ? ' (@' . $c->pseudo_tiktok . ')' : '' }}</option>
                    @empty
                    @endforelse
                </select>
                @if($createurs->isEmpty())
                <p class="mt-1 text-xs text-neon-orange">Aucun créateur dans votre périmètre. La liste est mise à jour à chaque chargement de la page.</p>
                @endif
            </div>
            <div>
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Montant (€)</label>
                <input type="number" name="amount" step="0.01" min="0.01" required class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm w-28 focus:outline-none focus:border-sky-500/50">
            </div>
            <div>
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Type</label>
                <select name="type" class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500/50">
                    <option value="virement">Virement</option>
                    <option value="carte-cadeau">Carte cadeau</option>
                    <option value="tiktok-live">TikTok Live</option>
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Notes</label>
                <input type="text" name="notes" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:outline-none focus:border-sky-500/50" placeholder="Optionnel">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white font-bold text-sm shadow-lg shadow-sky-500/20">Créer la demande</button>
        </form>
    </div>
    @endif

    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('withdrawals.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ !request('status') ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'bg-white/5 text-[#94a3b8] border border-white/10 hover:bg-white/10' }}">Toutes</a>
        <a href="{{ route('withdrawals.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request('status') === 'pending' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-white/5 text-[#94a3b8] border border-white/10 hover:bg-white/10' }}">En attente</a>
        <a href="{{ route('withdrawals.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request('status') === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-white/5 text-[#94a3b8] border border-white/10 hover:bg-white/10' }}">Approuvées</a>
        <a href="{{ route('withdrawals.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request('status') === 'rejected' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-white/5 text-[#94a3b8] border border-white/10 hover:bg-white/10' }}">Refusées</a>
    </div>

    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 bg-white/5">
                    <th class="text-left px-5 py-3 font-semibold text-white">Date</th>
                    <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Créateur</th>
                    <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Type</th>
                    <th class="text-right px-5 py-3 font-semibold text-white">Montant</th>
                    <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Statut</th>
                    @if(auth()->user()->canAddEntries())
                    <th class="px-5 py-3 font-semibold text-white text-right">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                    <td class="px-5 py-3.5 text-[#94a3b8]">{{ $w->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3.5 text-white font-medium">{{ $w->createur->nom }}</td>
                    <td class="px-5 py-3.5 text-[#94a3b8]">{{ $w->type }}</td>
                    <td class="px-5 py-3.5 text-right font-bold text-emerald-400">{{ number_format($w->amount, 2, ',', ' ') }} €</td>
                    <td class="px-5 py-3.5">
                        @if($w->status === 'pending')
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/20 text-amber-400 border border-amber-500/30">En attente</span>
                        @elseif($w->status === 'approved')
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Approuvée</span>
                        @else
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">Refusée</span>
                        @endif
                    </td>
                    @if(auth()->user()->canAddEntries())
                    <td class="px-5 py-3.5 text-right">
                        @if($w->status === 'pending')
                        <form action="{{ route('withdrawals.update', $w) }}" method="POST" class="inline">@csrf @method('PUT')<input type="hidden" name="status" value="approved"><button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 text-sm font-medium mr-1">Approuver</button></form>
                        <form action="{{ route('withdrawals.update', $w) }}" method="POST" class="inline">@csrf @method('PUT')<input type="hidden" name="status" value="rejected"><button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 text-sm font-medium">Refuser</button></form>
                        @else
                        <span class="text-[#64748b] text-sm">—</span>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->canAddEntries() ? 6 : 5 }}" class="px-5 py-12 text-center text-[#94a3b8]">Aucune demande.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($withdrawals->hasPages())
        <div class="px-5 py-4 border-t border-white/10">{{ $withdrawals->links() }}</div>
        @endif
    </div>
</div>
@endsection
