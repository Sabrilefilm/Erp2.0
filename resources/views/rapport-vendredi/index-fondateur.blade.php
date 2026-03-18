@extends('layouts.app')

@section('title', 'Rapports de la semaine')

@section('content')
<div class="space-y-6 pb-8">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-amber-500/20 via-orange-500/10 to-rose-500/10 border border-white/10 p-6 md:p-8">
        <h1 class="text-2xl md:text-3xl font-bold text-white">Rapports de la semaine</h1>
        <p class="text-[#94a3b8] text-sm mt-2">Traçabilité de tous les rapports. Objectif : faire évoluer les personnes et leur donner des consignes adaptées (ex. difficulté à trouver des matchs → les orienter vers d'autres agences ou leur manageur). Vous pouvez valider les rapports une fois lus.</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div class="rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 text-sm px-4 py-3">{{ session('info') }}</div>
    @endif

    <div class="ultra-card rounded-xl p-6 border border-white/10">
        <form method="get" action="{{ route('rapport-vendredi.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="annee" class="block text-xs font-medium text-[#94a3b8] mb-1">Année</label>
                <select name="annee" id="annee" class="ultra-input px-3 py-2 rounded-lg text-white text-sm">
                    @for($y = (int) now()->format('o'); $y >= (int) now()->format('o') - 2; $y--)
                    <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="semaine" class="block text-xs font-medium text-[#94a3b8] mb-1">Semaine</label>
                <select name="semaine" id="semaine" class="ultra-input px-3 py-2 rounded-lg text-white text-sm">
                    @for($s = 1; $s <= 53; $s++)
                    <option value="{{ $s }}" {{ $semaine == $s ? 'selected' : '' }}>Semaine {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="equipe_id" class="block text-xs font-medium text-[#94a3b8] mb-1">Agence</label>
                <select name="equipe_id" id="equipe_id" class="ultra-input px-3 py-2 rounded-lg text-white text-sm min-w-[180px]">
                    <option value="">Toutes</option>
                    @foreach($equipes as $eq)
                    <option value="{{ $eq->id }}" {{ request('equipe_id') == $eq->id ? 'selected' : '' }}>{{ $eq->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="user_id" class="block text-xs font-medium text-[#94a3b8] mb-1">Personne</label>
                <select name="user_id" id="user_id" class="ultra-input px-3 py-2 rounded-lg text-white text-sm min-w-[200px]">
                    <option value="">Toutes</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->getRoleLabel() }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-lg text-sm font-semibold">Filtrer</button>
        </form>
    </div>

    @php
        $libelleSemaine = \Carbon\Carbon::now()->setISODate($annee, $semaine)->startOfWeek()->format('d/m/Y');
    @endphp

    <h2 class="text-lg font-semibold text-white">Semaine du {{ $libelleSemaine }} ({{ count($rapports) }} rapport(s))</h2>

    @if($rapports->isEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center">
        <p class="text-[#94a3b8]">Aucun rapport pour cette semaine avec ces filtres.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($rapports as $r)
        <article class="ultra-card rounded-xl p-6 border {{ $r->isValide() ? 'border-emerald-500/30 bg-emerald-500/5' : 'border-white/10' }}">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-semibold text-white">{{ $r->user->name }}</p>
                    <p class="text-sm text-[#94a3b8]">{{ $r->user->getRoleLabel() }}{{ $r->user->equipe ? ' · ' . $r->user->equipe->nom : '' }}</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    @if($r->isValide())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 text-xs font-medium">
                        ✓ Validé le {{ $r->valide_at->translatedFormat('d/m/Y') }}@if($r->validePar) par {{ $r->validePar->name }}@endif
                    </span>
                    @else
                    <form action="{{ route('rapport-vendredi.valider', $r) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="annee" value="{{ request('annee', now()->format('o')) }}">
                        <input type="hidden" name="semaine" value="{{ request('semaine', now()->format('W')) }}">
                        @if(request('equipe_id'))<input type="hidden" name="equipe_id" value="{{ request('equipe_id') }}">@endif
                        @if(request('user_id'))<input type="hidden" name="user_id" value="{{ request('user_id') }}">@endif
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500/25 hover:bg-amber-500/35 text-amber-300 font-medium text-sm transition-colors">Valider le rapport</button>
                    </form>
                    @endif
                    <p class="text-xs text-[#64748b]">Enregistré le {{ $r->created_at->translatedFormat('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="text-[#b0bee3] whitespace-pre-wrap text-sm">{{ $r->contenu }}</div>
        </article>
        @endforeach
    </div>
    @endif
</div>
@endsection
