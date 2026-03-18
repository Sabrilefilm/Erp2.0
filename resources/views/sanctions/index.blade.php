@extends('layouts.app')

@section('title', 'Sanctions')

@section('content')
<div class="space-y-6 pb-8 max-w-4xl">
    {{-- Header style app — accueil --}}
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-sky-500/20 via-blue-500/10 to-indigo-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-2xl">
                    📋
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Sanctions</h1>
                    <p class="text-[#94a3b8] text-sm mt-0.5">Historique et suivi des avertissements</p>
                </div>
            </div>
            @if($sanctions->total() > 0)
            <div class="rounded-xl bg-white/5 border border-white/10 px-4 py-2 text-center sm:text-right">
                <p class="text-xs text-[#94a3b8] uppercase tracking-wider">Total</p>
                <p class="text-xl font-bold text-white">{{ $sanctions->total() }}</p>
            </div>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ {{ session('success') }}</div>
    @endif

    @if(auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur())
    {{-- Carte formulaire — style boutons ronds type "Demander / Recharger / Payer" --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <span class="text-xl">➕</span> Enregistrer une sanction
        </h2>
        <form action="{{ route('sanctions.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Créateur</label>
                    <select name="createur_id" required class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500/50 focus:ring-1 focus:ring-sky-500/30">
                        <option value="">Choisir un créateur</option>
                        @foreach($createurs as $c)
                        <option value="{{ $c->id }}">{{ $c->nom }}{{ $c->pseudo_tiktok ? ' (@' . $c->pseudo_tiktok . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Type</label>
                    <select name="type" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500/50">
                        <option value="Avertissement">Avertissement</option>
                        <option value="Blâme">Blâme</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Exclusion">Exclusion</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Niveau</label>
                    <select name="niveau" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500/50">
                        <option value="agence">Agence</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Raison (optionnel)</label>
                <input type="text" name="raison" placeholder="Ex. Non-respect des règles…" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:outline-none focus:border-sky-500/50">
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-400 text-white font-bold text-base transition-all shadow-lg shadow-sky-500/30 hover:shadow-sky-500/40 hover:scale-[1.02] active:scale-[0.98] border-0 cursor-pointer focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-[#0f172a]">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Enregistrer
            </button>
        </form>
    </div>
    @endif

    {{-- Fil d'activité groupé par date — style app --}}
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="px-5 py-4 border-b border-white/10">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">Historique</h2>
        </div>

        @if($sanctions->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4 text-4xl">📋</div>
            <p class="text-[#94a3b8]">Aucune sanction pour l’instant.</p>
        </div>
        @else
        @php
            $grouped = $sanctions->groupBy(fn ($s) => $s->created_at->format('Y-m-d'));
        @endphp
        <div class="divide-y divide-white/5">
            @foreach($grouped as $date => $items)
            <div>
                <div class="px-5 py-2.5 bg-white/5 text-xs font-bold text-[#94a3b8] uppercase tracking-wider">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                </div>
                @foreach($items as $s)
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 hover:bg-white/5 transition-colors border-b border-white/5 last:border-0">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-full bg-sky-500/20 flex items-center justify-center shrink-0 text-sky-400 font-bold text-sm">
                            {{ strtoupper(mb_substr($s->createur->nom, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-white">{{ $s->createur->nom }}</p>
                            <p class="text-sm text-[#94a3b8] truncate">{{ $s->raison ?: '—' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        @if($s->type === 'Avertissement')
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/20 text-amber-400 border border-amber-500/30">Avertissement</span>
                        @elseif($s->type === 'Blâme')
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-500/20 text-orange-400 border border-orange-500/30">Blâme</span>
                        @elseif($s->type === 'Suspension')
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">Suspension</span>
                        @else
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-500/20 text-rose-400 border border-rose-500/30">Exclusion</span>
                        @endif
                        <span class="text-xs text-[#64748b]">{{ $s->created_at->format('H:i') }}</span>
                        @if($s->attribuePar)
                        <span class="text-xs text-[#64748b]">par {{ $s->attribuePar->name }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @if($sanctions->hasPages())
        <div class="px-5 py-4 border-t border-white/10">{{ $sanctions->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection
