@extends('layouts.app')

@section('title', 'Matchs')

@push('styles')
<style>
/* Page Matchs — design simple et moderne */
.match-page {
    min-height: 100vh;
    background: #0b0f1a;
    color: #e2e8f0;
}
.match-hero {
    animation: match-fade-in 0.5s ease-out;
}
@keyframes match-fade-in {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
.match-main {
    animation: match-fade-in 0.5s ease-out 0.1s backwards;
}
.match-section {
    animation: match-fade-in 0.4s ease-out backwards;
}
.match-section:nth-of-type(1){ animation-delay: 0.08s; }
.match-section:nth-of-type(2){ animation-delay: 0.14s; }
.match-section:nth-of-type(3){ animation-delay: 0.2s; }
.match-section:nth-of-type(4){ animation-delay: 0.26s; }
.match-section:nth-of-type(5){ animation-delay: 0.32s; }
.match-section:nth-of-type(6){ animation-delay: 0.38s; }
.match-section:nth-of-type(n+7){ animation-delay: 0.42s; }
/* Cartes : entrée en cascade + hover */
.match-list > li {
    animation: match-card-in 0.4s cubic-bezier(0.22, 0.61, 0.36, 1) backwards;
}
.match-list > li:nth-child(1){ animation-delay: 0.06s; }
.match-list > li:nth-child(2){ animation-delay: 0.1s; }
.match-list > li:nth-child(3){ animation-delay: 0.14s; }
.match-list > li:nth-child(4){ animation-delay: 0.18s; }
.match-list > li:nth-child(5){ animation-delay: 0.22s; }
.match-list > li:nth-child(6){ animation-delay: 0.26s; }
.match-list > li:nth-child(7){ animation-delay: 0.3s; }
.match-list > li:nth-child(8){ animation-delay: 0.34s; }
.match-list > li:nth-child(n+9){ animation-delay: 0.38s; }
@keyframes match-card-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.match-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
}
.match-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px -8px rgba(0,0,0,0.4);
    border-color: rgba(6, 182, 212, 0.2);
    background: rgba(255,255,255,0.06);
}
/* Onglets */
.match-tabs a {
    transition: color 0.2s, border-color 0.2s, opacity 0.2s;
}
.match-tabs a:hover { opacity: 1; }
/* Filtres */
.match-filters-block[open] .details-chevron { transform: rotate(180deg); }
.match-filters-block summary { transition: background 0.2s; }
/* Badge "bientôt" */
.match-bientot-badge { display: inline-flex; align-items: center; gap: 4px; font-weight: 600; white-space: nowrap; }
.match-bientot-10 { animation: match-bientot-pulse 1.5s ease-in-out infinite; }
.match-bientot-5 { animation: match-bientot-pulse 0.8s ease-in-out infinite; box-shadow: 0 0 12px rgba(244,63,94,0.4); }
@keyframes match-bientot-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.9; transform: scale(1.04); }
}
/* Empty state */
.match-empty {
    animation: match-fade-in 0.5s ease-out 0.15s backwards;
}
</style>
@endpush

@section('content')
@php
    $queryBase = array_filter(request()->only(['from', 'to', 'type', 'createur_id', 'statut', 'equipe_id']), fn($v) => $v !== null && $v !== '');
    $vueReq = request()->get('vue', '');
    $basePourPartenaireUnions = ($vueReq === 'aujourdhui') ? array_merge($queryBase, ['from' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'to' => now()->addMonth()->endOfMonth()->format('Y-m-d')]) : $queryBase;
    if (!isset($urlAujourdhui)) { $urlAujourdhui = route('matches.index', array_merge($queryBase, ['vue' => 'aujourdhui', 'from' => now()->format('Y-m-d'), 'to' => now()->format('Y-m-d')])); }
    if (!isset($urlPartenaire)) { $urlPartenaire = route('matches.index', array_merge($basePourPartenaireUnions, ['vue' => 'partenaire'])); }
    if (!isset($urlUnions)) { $urlUnions = route('matches.index', array_merge($basePourPartenaireUnions, ['vue' => 'unions'])); }
@endphp
<div class="match-page min-h-screen pb-24 md:pb-12">
    <header class="match-hero sticky top-0 z-10 border-b border-white/10 bg-[#0b0f1a]/90 backdrop-blur-md md:static">
        <div class="max-w-3xl mx-auto px-4 md:px-6 py-4 flex items-center justify-between gap-3">
            <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight">Matchs</h1>
            @if(auth()->user()->canProgrammerMatch())
            <a href="{{ route('matches.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-cyan-500/25 transition hover:bg-cyan-400 hover:shadow-cyan-500/30 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2 focus:ring-offset-[#0b0f1a]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                P-Match
            </a>
            @endif
        </div>
    </header>

    <main class="match-main max-w-3xl mx-auto px-4 md:px-6 py-6 space-y-5">
        @if(session('success'))
        <div class="match-section rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-sm px-4 py-3">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="match-section rounded-xl bg-red-500/15 border border-red-500/30 text-red-400 text-sm px-4 py-3">{{ session('error') }}</div>
        @endif

        {{-- Matchs aujourd'hui et demain --}}
        @if(($vue ?? '') !== 'aujourdhui' && isset($matchsAujourdhuiEtDemain) && $matchsAujourdhuiEtDemain->isNotEmpty())
        <section class="match-section rounded-xl border border-cyan-500/20 bg-cyan-500/5 p-4">
            <h2 class="text-sm font-semibold text-white mb-1">Aujourd'hui & demain</h2>
            <p class="text-xs text-slate-400 mb-2">Priorité pour vous mélanger et interagir. <span class="text-slate-500">Un match TikTok = 5 min.</span></p>
            <ul class="space-y-1.5">
                @foreach($matchsAujourdhuiEtDemain as $ma)
                @php
                    $sm = $ma->statut ?? 'programme';
                    $dateMatch = $ma->date ? \Carbon\Carbon::parse($ma->date)->startOfDay() : null;
                    $estDemain = $dateMatch && $dateMatch->isTomorrow();
                    $estAujourdhui = $dateMatch && $dateMatch->isToday();
                    $jourLabel = $estDemain ? 'Demain' : 'Aujourd\'hui';
                    $bientotLabel = null;
                    $bientotClass = '';
                    if ($estAujourdhui && $ma->heure) {
                        $matchDt = \Carbon\Carbon::parse($ma->date->format('Y-m-d') . ' ' . $ma->heure);
                        if ($matchDt->isFuture()) {
                            $minutesRestantes = (int) $matchDt->diffInMinutes(now(), false);
                            if ($minutesRestantes <= 5 && $minutesRestantes > 0) {
                                $bientotLabel = 'Dans 5 min';
                                $bientotClass = 'match-bientot-5 px-1.5 py-0.5 rounded text-[10px] bg-rose-500/30 text-rose-300 border border-rose-400/50';
                            } elseif ($minutesRestantes <= 10 && $minutesRestantes > 5) {
                                $bientotLabel = 'Dans 10 min';
                                $bientotClass = 'match-bientot-10 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/25 text-amber-300 border border-amber-400/40';
                            }
                        }
                    }
                @endphp
                <li class="rounded-lg bg-white/[0.03] border border-white/5 text-xs overflow-hidden {{ $bientotLabel ? 'ring-1 ring-amber-400/30' : '' }}">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 py-1.5 px-2">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $estDemain ? 'bg-cyan-500/25 text-cyan-300' : 'bg-emerald-500/25 text-emerald-300' }}">{{ $jourLabel }}</span>
                        @if($bientotLabel)<span class="match-bientot-badge {{ $bientotClass }}">⏱ {{ $bientotLabel }}</span>@endif
                        <span class="tabular-nums text-[#94a3b8]">{{ $ma->heure ? substr($ma->heure, 0, 5) : '—' }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-300">{{ $ma->type === 'match_off' ? ($ma->avec_boost ? 'Match officiel avec boost' : 'Match officiel sans boost') . ($ma->niveau_match ? ' · ' . (\App\Models\Planning::NIVEAUX_MATCH_OFF[$ma->niveau_match] ?? $ma->niveau_match) : '') : ($typeLabels[$ma->type] ?? $ma->type) }}</span>
                        <span class="text-white truncate max-w-[120px]">{{ $ma->createur?->nom ?? '—' }}</span>
                        <span class="vs-animated">Vs</span>
                        <span class="text-cyan-300 truncate max-w-[100px]">@if($ma->createur_adverse_at){{ str_starts_with($ma->createur_adverse_at, '@') ? $ma->createur_adverse_at : '@'.$ma->createur_adverse_at }}@else—@endif</span>
                        @if($ma->createur && $ma->createur->equipe)<span class="text-[10px] text-[#64748b]">({{ $ma->createur->equipe->nom }})</span>@endif
                        @if($sm === 'en_cours')<span class="ml-auto px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400">En cours</span>@endif
                    </div>
                    @if(auth()->user()->isCreateur())
                    <div class="px-2 pb-1.5 pt-0 border-t border-white/5 text-[10px] text-[#64748b]">
                        Affiché 7/7 24/24 @if($ma->creePar) · Par {{ $ma->creePar->name }}@endif
                        @if($ma->updated_par && $ma->updated_at)<span class="text-amber-400/90"> · Modifié le {{ $ma->updated_at->translatedFormat('d/m/Y') }}</span>@endif
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </section>
        @endif

        {{-- Mes prochains matchs (créateur) --}}
        @if(auth()->user()->isCreateur() && $prochainsMatchs->isNotEmpty())
        <section class="match-section rounded-xl border border-white/10 bg-white/5 p-4">
            <h2 class="text-sm font-semibold text-white mb-2">Mes prochains matchs</h2>
            <div class="space-y-1.5">
                @foreach($prochainsMatchs as $prochain)
                @php
                    $jour = $prochain->date->copy()->startOfDay();
                    $today = now()->startOfDay();
                    $diff = (int) $today->diffInDays($jour, false);
                    if ($diff < 0) continue;
                    $jLabel = $diff === 0 ? 'Aujourd\'hui' : ($diff === 1 ? 'Demain' : 'J-' . $diff);
                    $bientotProchain = null;
                    $bientotProchainClass = '';
                    if ($diff === 0 && $prochain->heure) {
                        $matchDtP = \Carbon\Carbon::parse($prochain->date->format('Y-m-d') . ' ' . $prochain->heure);
                        if ($matchDtP->isFuture()) {
                            $minP = (int) $matchDtP->diffInMinutes(now(), false);
                            if ($minP <= 5 && $minP > 0) { $bientotProchain = 'Dans 5 min'; $bientotProchainClass = 'match-bientot-5 px-1.5 py-0.5 rounded text-[10px] bg-rose-500/30 text-rose-300 border border-rose-400/50'; }
                            elseif ($minP <= 10 && $minP > 5) { $bientotProchain = 'Dans 10 min'; $bientotProchainClass = 'match-bientot-10 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/25 text-amber-300 border border-amber-400/40'; }
                        }
                    }
                @endphp
                <div class="rounded-lg bg-white/[0.03] border border-white/5 overflow-hidden {{ $bientotProchain ? 'ring-1 ring-amber-400/30' : '' }}">
                    <div class="flex flex-wrap items-center gap-x-2 text-xs py-1.5 px-2">
                        <span class="font-medium text-cyan-400">{{ $jLabel }}</span>
                        @if($bientotProchain)<span class="match-bientot-badge {{ $bientotProchainClass }}">⏱ {{ $bientotProchain }}</span>@endif
                        <span class="text-[#94a3b8]">·</span>
                        <span class="text-white">{{ $prochain->type === 'match_off' ? ($prochain->avec_boost ? 'Match officiel avec boost' : 'Match officiel sans boost') . ($prochain->niveau_match ? ' · ' . (\App\Models\Planning::NIVEAUX_MATCH_OFF[$prochain->niveau_match] ?? $prochain->niveau_match) : '') : ($typeLabels[$prochain->type] ?? $prochain->type) }}</span>
                        @if($prochain->heure)<span class="text-white">{{ substr($prochain->heure, 0, 5) }}</span>@endif
                        <span class="text-white">{{ $prochain->createur?->nom ?? '—' }}</span>
                        <span class="text-[#94a3b8]">vs</span>
                        @if($prochain->createur_adverse_at)<span class="text-cyan-300">{{ str_starts_with($prochain->createur_adverse_at, '@') ? $prochain->createur_adverse_at : '@'.$prochain->createur_adverse_at }}</span>@else<span class="text-[#64748b]">—</span>@endif
                    </div>
                    <div class="px-2 pb-1.5 pt-0 border-t border-white/5 text-[10px] text-[#64748b] space-y-0.5">
                        @if($prochain->createur_adverse || $prochain->createur_adverse_agent)
                        <div class="text-[#94a3b8]">
                            @if($prochain->createur_adverse)<span class="text-white/90">{{ $prochain->createur_adverse }}</span>@endif
                            @if($prochain->createur_adverse_agent)@if($prochain->createur_adverse) · @endif<span>Agent {{ $prochain->createur_adverse_agent }}</span>@endif
                        </div>
                        @endif
                        Affiché 7/7 24/24 @if($prochain->creePar) · Par {{ $prochain->creePar->name }}@endif
                        @if($prochain->updated_par && $prochain->updated_at)<span class="text-amber-400/90"> · Modifié le {{ $prochain->updated_at->translatedFormat('d/m/Y') }}</span>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Onglets --}}
        @if(!empty($showTabsVue))
        <nav class="match-section match-tabs flex flex-wrap items-center gap-1 border-b border-white/10 -mx-4 px-4 md:mx-0 md:px-0 pb-2" role="tablist">
            <a href="{{ $urlAujourdhui }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ ($vue ?? '') === 'aujourdhui' ? 'border-amber-400 text-amber-400' : 'border-transparent text-slate-400 hover:text-white' }}">Aujourd'hui</a>
            <a href="{{ $urlUnions }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ ($vue ?? '') === 'unions' ? 'border-red-400 text-red-400' : 'border-transparent text-slate-400 hover:text-white' }}">Match Unions</a>
            <a href="{{ $urlPartenaire }}" class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 {{ ($vue ?? '') === 'partenaire' ? 'border-cyan-400 text-cyan-400' : 'border-transparent text-slate-400 hover:text-white' }}">Match partenaire</a>
            @if(!empty($showCatalogueEquipeSelect) && ($vue ?? '') === 'partenaire')
            <form action="{{ route('matches.index') }}" method="GET" class="ml-auto flex items-center gap-2">
                @foreach(request()->only(['vue', 'from', 'to', 'type', 'createur_id', 'statut']) as $k => $v)
                    @if($v !== null && $v !== '')<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                @endforeach
                <select name="equipe_id" onchange="this.form.submit()" class="ultra-input px-2 py-1.5 rounded-lg text-white text-xs border-0 bg-white/5 min-w-[120px]">
                    <option value="">Toutes les équipes</option>
                    @foreach($equipes as $eq)
                    <option value="{{ $eq->id }}" {{ (string)($filterEquipeId ?? '') === (string)$eq->id ? 'selected' : '' }}>{{ $eq->nom }}</option>
                    @endforeach
                </select>
            </form>
            @endif
        </nav>
        @endif

        {{-- Demandes à traiter (agents) --}}
        @if(auth()->user()->canProgrammerMatch())
        <section class="match-section rounded-xl border border-amber-500/25 bg-amber-500/5 p-4" aria-labelledby="demandes-titre">
            <h2 id="demandes-titre" class="text-sm font-semibold text-amber-400 mb-2">Demandes à traiter</h2>
            @if($demandes->isNotEmpty())
            <ul class="space-y-1.5">
                @foreach($demandes as $d)
                @php
                    $params = ['createur_id' => $d->createur_id, 'date' => $d->date_souhaitee->format('Y-m-d'), 'type' => $d->type];
                    if ($d->heure_souhaitee) $params['heure'] = $d->heure_souhaitee;
                    if ($d->qui_en_face) {
                        $params['createur_adverse_at'] = preg_replace('/^@/', '', trim($d->qui_en_face));
                    }
                @endphp
                <li class="flex flex-wrap items-center justify-between gap-x-2 gap-y-1 py-1.5 px-2 rounded-lg bg-white/5 border border-white/5 text-xs">
                    <span class="font-medium text-white">{{ $d->createur?->nom ?? '—' }}</span>
                    <span class="text-[#94a3b8]">{{ $d->date_souhaitee->format('d/m') }} · {{ $typeLabels[$d->type] ?? $d->type }}</span>
                    @if($d->qui_en_face)<span class="text-[#64748b]">vs {{ $d->qui_en_face }}</span>@endif
                    <div class="ml-auto flex gap-1">
                        <a href="{{ route('matches.create', $params) }}" class="ultra-btn-cta px-2 py-1 text-[11px]"><span>Accepter</span></a>
                        <a href="{{ route('matches.create', $params) }}" class="px-2 py-1 rounded bg-white/10 text-[#94a3b8] text-[11px]">Modifier</a>
                        <form action="{{ route('matches.demande.refuse', $d) }}" method="POST" class="inline" onsubmit="return confirm('Refuser cette demande ?');">@csrf
                            <button type="submit" class="px-2 py-1 rounded bg-red-500/20 text-red-400 text-[11px] border-0 cursor-pointer">Refuser</button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-[#94a3b8] text-xs py-1">Aucune demande en attente. Les demandes des créateurs dont vous êtes l’agent apparaîtront ici.</p>
            @endif
        </section>
        @endif

        {{-- Filtres --}}
        <details class="match-section match-filters-block rounded-xl border border-white/10 bg-white/5 overflow-hidden" @if(request()->hasAny(['from','to','type','createur_id','statut','vue','equipe_id'])) open @endif>
            <summary class="px-4 py-3 cursor-pointer list-none flex items-center justify-between gap-3 hover:bg-white/5 transition-colors">
                <span class="flex items-center gap-2 text-sm font-semibold text-white">
                    <svg class="w-5 h-5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtres
                </span>
                <span class="text-slate-400 text-xs tabular-nums">{{ \Carbon\Carbon::parse($from)->format('d/m') }} → {{ \Carbon\Carbon::parse($to)->format('d/m') }}</span>
                <svg class="w-5 h-5 text-cyan-400 shrink-0 details-chevron transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <form action="{{ route('matches.index') }}" method="GET" class="p-4 pt-2 grid grid-cols-2 md:flex md:flex-wrap gap-3 border-t border-white/10 bg-black/20">
                @if(!empty($showTabsVue) && isset($vue) && $vue !== '')<input type="hidden" name="vue" value="{{ $vue }}">@endif
                @if(!empty($filterEquipeId))<input type="hidden" name="equipe_id" value="{{ $filterEquipeId }}">@endif
                <div class="min-w-[120px]"><label class="block text-xs font-medium text-slate-300 mb-1">Du</label><input type="date" name="from" value="{{ $from }}" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm border border-white/10 bg-white/5"></div>
                <div class="min-w-[120px]"><label class="block text-xs font-medium text-slate-300 mb-1">Au</label><input type="date" name="to" value="{{ $to }}" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm border border-white/10 bg-white/5"></div>
                <div class="min-w-[140px]"><label class="block text-xs font-medium text-slate-300 mb-1">Type</label><select name="type" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm border border-white/10 bg-white/5"><option value="">Tous</option>@foreach($typeLabels as $v => $l)<option value="{{ $v }}" @if($filterType === $v) selected @endif>{{ $l }}</option>@endforeach</select></div>
                @if(!auth()->user()->isCreateur())
                <div class="min-w-[160px]"><label class="block text-xs font-medium text-slate-300 mb-1">Créateur</label><select name="createur_id" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm border border-white/10 bg-white/5"><option value="">Tous</option>@foreach($createurs as $c)<option value="{{ $c->id }}" @if((string)$filterCreateurId === (string)$c->id) selected @endif>{{ $c->nom }}</option>@endforeach</select></div>
                @endif
                <div class="min-w-[120px]"><label class="block text-xs font-medium text-slate-300 mb-1">Statut</label><select name="statut" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm border border-white/10 bg-white/5"><option value="">Tous</option>@foreach($statutLabels as $v => $l)<option value="{{ $v }}" @if($filterStatut === $v) selected @endif>{{ $l }}</option>@endforeach</select></div>
                <div class="col-span-2 md:col-span-1 flex items-end"><button type="submit" class="ultra-btn-cta w-full md:w-auto px-4 py-2.5 text-sm"><span>Appliquer</span></button></div>
            </form>
        </details>

        {{-- Titre catalogue + résumé --}}
        <div class="flex flex-wrap items-center justify-between gap-2">
            @if($vue === 'partenaire' && $showCatalogueEquipe)
            <h2 class="text-xs font-semibold text-cyan-400">Match partenaire — ma team</h2>
            @elseif($vue === 'unions')
            <h2 class="text-xs font-semibold text-red-400/90">Match Unions</h2>
            @else
            <span></span>
            @endif
            <p class="text-xs text-[#94a3b8] flex items-center gap-2 flex-wrap">
                @if($matchs->total() > 0)
                <span class="font-semibold text-white">{{ $matchs->total() }}</span> match{{ $matchs->total() > 1 ? 's' : '' }}
                @else
                Aucun match
                @endif
                <a href="{{ route('matches.pdf', request()->query()) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 text-xs font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exporter en PDF
                </a>
            </p>
        </div>

        {{-- Titre liste : sur Aujourd'hui = matchs à venir sur 7 jours ; sinon = autres matchs --}}
        @if(($vue ?? '') === 'aujourdhui')
        <p class="text-xs font-semibold text-white/70 mb-2">Matchs à venir — 7 jours (2 agences)</p>
        @elseif(isset($matchsAujourdhuiEtDemain) && $matchsAujourdhuiEtDemain->isNotEmpty())
        <p class="text-xs font-semibold text-white/70 mb-2">Autres matchs (hors aujourd'hui et demain)</p>
        @endif
        @if($matchs->isEmpty())
        <div class="match-empty rounded-xl border border-white/10 bg-white/5 p-10 text-center {{ ($vue ?? '') === 'partenaire' ? 'border-cyan-500/20 bg-cyan-500/5' : '' }}">
            @if(($vue ?? '') === 'partenaire')
            <p class="text-4xl mb-3 opacity-80" aria-hidden="true">📅</p>
            <p class="text-cyan-300 font-semibold text-sm">Aucun match partenaire</p>
            <p class="text-slate-400 text-xs mt-1">Passe par <strong class="text-white/80">P-Match</strong> pour en programmer un.</p>
            @else
            <p class="text-3xl mb-3 opacity-50" aria-hidden="true">📅</p>
            @if(isset($matchsAujourdhuiEtDemain) && $matchsAujourdhuiEtDemain->isNotEmpty())
            <p class="text-slate-400 text-sm">Aucun autre match sur cette période.</p>
            @else
            @if(($vue ?? '') === 'aujourdhui')
            <p class="text-slate-400 text-sm">Aucun match à venir sur les 7 prochains jours.</p>
            @else
            <p class="text-slate-400 text-sm">Aucun match ({{ \Carbon\Carbon::parse($from)->translatedFormat('d M') }} → {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}).</p>
            @endif
            <p class="text-slate-500 text-xs mt-1">Ouvre les <strong class="text-white/70">Filtres</strong> ou crée un match avec <strong class="text-white/70">P-Match</strong>.</p>
            @endif
            @endif
        </div>
        @else
        <ul class="space-y-4 match-list">
            @foreach($matchs as $m)
            @php
                $s = $m->statut ?? 'programme';
                $typeLabel = $m->type === 'match_off'
                    ? ($m->avec_boost ? 'Match officiel avec boost' : 'Match officiel sans boost')
                        . ($m->niveau_match ? ' · ' . (\App\Models\Planning::NIVEAUX_MATCH_OFF[$m->niveau_match] ?? $m->niveau_match) : '')
                    : ($typeLabels[$m->type] ?? $m->type);
                $adverseAt = $m->createur_adverse_at ? (str_starts_with($m->createur_adverse_at, '@') ? $m->createur_adverse_at : '@'.$m->createur_adverse_at) : '—';
                $bientotCard = null;
                $bientotCardClass = '';
                if ($s !== 'en_cours' && $m->date && $m->date->isToday() && $m->heure) {
                    $matchDtC = \Carbon\Carbon::parse($m->date->format('Y-m-d') . ' ' . $m->heure);
                    if ($matchDtC->isFuture()) {
                        $minC = (int) $matchDtC->diffInMinutes(now(), false);
                        if ($minC <= 5 && $minC > 0) { $bientotCard = 'Dans 5 min'; $bientotCardClass = 'match-bientot-5 px-2 py-1 rounded-full text-[10px] font-semibold bg-rose-500/30 text-rose-300 border border-rose-400/50'; }
                        elseif ($minC <= 10 && $minC > 5) { $bientotCard = 'Dans 10 min'; $bientotCardClass = 'match-bientot-10 px-2 py-1 rounded-full text-[10px] font-semibold bg-amber-500/25 text-amber-300 border border-amber-400/40'; }
                    }
                }
            @endphp
            <li class="match-card rounded-2xl border border-white/10 bg-white/[0.06] overflow-hidden relative {{ $bientotCard ? 'ring-1 ring-amber-400/40' : '' }}">
                {{-- Tag statut (pilule en haut à gauche) + badge bientôt --}}
                <div class="pt-4 px-4 pb-1">
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium
                        @if($s === 'en_cours') bg-amber-500/25 text-amber-400
                        @elseif($s === 'acceptee') bg-emerald-500/25 text-emerald-400
                        @elseif($s === 'manque') bg-red-500/25 text-red-400
                        @elseif($s === 'refusee') bg-orange-500/25 text-orange-400
                        @else bg-slate-500/25 text-slate-300
                        @endif">
                        @if($s === 'en_cours')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                            En cours
                        @elseif($s === 'acceptee') OK
                        @elseif($s === 'manque') Manqué
                        @elseif($s === 'refusee') Refusée
                        @else Programmé
                        @endif
                    </span>
                    @if($bientotCard)<span class="match-bientot-badge ml-2 {{ $bientotCardClass }}">⏱ {{ $bientotCard }}</span>@endif
                    @if($m->updated_par)<span class="ml-2 inline-flex rounded-full px-2 py-0.5 text-[10px] bg-amber-500/20 text-amber-400">Modif</span>@endif
                </div>
                {{-- Titre principal : Créateur Vs @adverse --}}
                <div class="px-4 pb-2">
                    <h3 class="text-base sm:text-lg font-semibold text-white leading-tight truncate" title="{{ $m->createur?->nom ?? '—' }} Vs {{ $adverseAt }}">
                        <span class="text-white">{{ $m->createur?->nom ?? '—' }}</span>
                        <span class="vs-animated mx-1">Vs</span>
                        <span class="text-cyan-400">{{ $adverseAt }}</span>
                    </h3>
                </div>
                {{-- Bouton d'action principal --}}
                <div class="px-4 pb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @if(auth()->user()->canProgrammerMatch())
                        <a href="{{ route('matches.edit', $m) }}" class="ultra-btn-cta inline-flex items-center justify-center text-sm px-4 py-2.5"><span>Modifier</span></a>
                        @endif
                        @if((auth()->user()->isFondateur() || auth()->user()->isManageur()) && ($m->createur_adverse_numero || $m->createur_adverse_at || $m->createur_adverse || $m->createur_adverse_agent || $m->createur_adverse_email || $m->createur_adverse_agence || $m->createur_adverse_autres))
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-cyan-500/40 bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 text-sm px-3 py-2 js-open-infos-adverse"
                                data-at="{{ $m->createur_adverse_at ?? '' }}"
                                data-nom-agence="{{ $m->createur_adverse ?? '' }}"
                                data-agence="{{ $m->createur_adverse_agence ?? '' }}"
                                data-agent="{{ $m->createur_adverse_agent ?? '' }}"
                                data-numero="{{ $m->createur_adverse_numero ?? '' }}"
                                data-email="{{ $m->createur_adverse_email ?? '' }}"
                                data-autres="{{ $m->createur_adverse_autres ?? '' }}" title="Voir les infos adverse">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Infos
                        </button>
                        @endif
                        @if(auth()->user()->canProgrammerMatch())
                        <form action="{{ route('matches.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce match ?');">@csrf @method('DELETE')
                            <button type="submit" class="rounded-xl border border-red-500/30 bg-transparent text-red-400 hover:bg-red-500/10 text-sm px-3 py-2 transition-colors">Supprimer</button>
                        </form>
                        @endif
                    </div>
                </div>
                {{-- Ligne infos avec icônes (date/heure, type, équipe) --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 px-4 py-3 border-t border-white/5 bg-black/10 text-xs text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <time class="tabular-nums">{{ $m->date->translatedFormat('d/m') }}@if($m->heure) {{ substr($m->heure, 0, 5) }}@else —@endif</time>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>{{ $typeLabel }}</span>
                    </span>
                    @if($m->createur?->equipe)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>{{ $m->createur->equipe->nom }}</span>
                    </span>
                    @endif
                </div>
                {{-- Créateur : 7/7 24/24, par qui, modifié le --}}
                @if(auth()->user()->isCreateur())
                <div class="px-4 py-2 border-t border-white/5 text-[10px] text-slate-500">
                    Affiché 7/7 24/24
                    @if($m->creePar)· Par {{ $m->creePar->name }}@endif
                    @if($m->updated_par && $m->updated_at)<span class="text-amber-400/90"> · Modifié le {{ $m->updated_at->translatedFormat('d/m/Y') }}</span>@endif
                </div>
                @endif
            </li>
            @endforeach
        </ul>

        @if($matchs->hasPages())
        <div class="flex justify-center py-4">{{ $matchs->links() }}</div>
        @endif
        @endif

        {{-- Lien historique --}}
        @if($matchs->total() > 0 && $matchs->hasPages())
        <p class="text-center">
            <a href="{{ route('matches.index', array_merge(request()->query(), ['from' => now()->startOfYear()->format('Y-m-d'), 'to' => now()->format('Y-m-d')])) }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium">Voir tout l'historique</a>
        </p>
        @endif

        {{-- Bouton Demander un match (créateurs) --}}
        @if(auth()->user()->isCreateur())
        <div class="pt-4 pb-4">
            <a href="{{ route('matches.demande.create') }}" class="ultra-btn-cta btn-demande-match">
                <span class="btn-demande-shimmer" aria-hidden="true"></span>
                <span class="btn-demande-text">Demander un match</span>
            </a>
        </div>
        @endif
    </main>
</div>

{{-- Fenêtre modale : infos créateur adverse — rendue dans body pour affichage correct sur PC --}}
@if(auth()->user()->isFondateur() || auth()->user()->isManageur())
<div id="modal-infos-adverse" class="modal-infos-adverse-overlay hidden" aria-modal="true" role="dialog" aria-labelledby="modal-infos-adverse-title">
    <div class="modal-infos-adverse-box bg-[#1e293b] border border-white/10 rounded-2xl shadow-xl w-full max-w-[min(22rem,calc(100vw-2rem))] sm:max-w-md p-5 sm:p-6 relative max-h-[90vh] overflow-y-auto" @click.stop>
        <h2 id="modal-infos-adverse-title" class="text-base sm:text-lg font-semibold text-white mb-4 pr-8">Informations créateur adverse</h2>
        <div class="space-y-4 text-sm">
            <div>
                <span class="text-[#64748b] block text-xs mb-0.5">@ (compte TikTok)</span>
                <span id="modal-infos-at" class="text-cyan-300 font-medium text-base">—</span>
            </div>
            <div>
                <span class="text-[#64748b] block text-xs mb-0.5">Nom / Agence</span>
                <span id="modal-infos-nom-agence" class="text-white font-medium break-words">—</span>
            </div>
            <div id="modal-infos-agence-wrap" style="display: none;">
                <span class="text-[#64748b] block text-xs mb-0.5">Agence</span>
                <span id="modal-infos-agence" class="text-white font-medium break-words">—</span>
            </div>
            <div id="modal-infos-agent-wrap" style="display: none;">
                <span class="text-[#64748b] block text-xs mb-0.5">Agent / e</span>
                <span id="modal-infos-agent" class="text-white font-medium break-words">—</span>
            </div>
            <div>
                <span class="text-[#64748b] block text-xs mb-1.5">Téléphone</span>
                <span id="modal-infos-numero" class="text-white font-semibold text-base block mb-2 select-all select-text tabular-nums">—</span>
                <div id="modal-infos-tel-actions" class="flex flex-col sm:flex-row sm:flex-wrap gap-2 hidden">
                    <a id="modal-infos-tel-prive" href="#" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-white/10 text-[#94a3b8] hover:text-white hover:bg-white/15 text-xs font-medium transition-colors min-h-[44px] sm:min-h-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Appeler (privé)
                    </a>
                    <a id="modal-infos-whatsapp" href="#" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-green-500/20 text-green-400 hover:bg-green-500/30 text-xs font-medium transition-colors min-h-[44px] sm:min-h-0">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.865 9.865 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <button type="button" id="modal-infos-copy-btn" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-white/10 text-[#94a3b8] hover:text-white hover:bg-white/15 text-xs font-medium transition-colors min-h-[44px] sm:min-h-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copier le numéro
                    </button>
                </div>
            </div>
            <div id="modal-infos-email-wrap" style="display: none;">
                <span class="text-[#64748b] block text-xs mb-0.5">E-mail</span>
                <a id="modal-infos-email" href="#" class="text-cyan-300 font-medium break-all hover:underline">—</a>
            </div>
            <div id="modal-infos-autres-wrap" style="display: none;">
                <span class="text-[#64748b] block text-xs mb-0.5">Autres infos</span>
                <p id="modal-infos-autres" class="text-white font-normal break-words whitespace-pre-wrap mt-0">—</p>
            </div>
        </div>
        <button type="button" class="absolute top-3 right-3 w-8 h-8 rounded-lg flex items-center justify-center text-[#94a3b8] hover:text-white hover:bg-white/10 transition-colors js-close-modal-infos" aria-label="Fermer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
<script>
(function() {
    var modal = document.getElementById('modal-infos-adverse');
    if (!modal) return;
    if (document.body && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }
    var atEl = document.getElementById('modal-infos-at');
    var nomAgenceEl = document.getElementById('modal-infos-nom-agence');
    var agenceEl = document.getElementById('modal-infos-agence');
    var agenceWrap = document.getElementById('modal-infos-agence-wrap');
    var agentEl = document.getElementById('modal-infos-agent');
    var agentWrap = document.getElementById('modal-infos-agent-wrap');
    var numeroEl = document.getElementById('modal-infos-numero');
    var telActions = document.getElementById('modal-infos-tel-actions');
    var telPrive = document.getElementById('modal-infos-tel-prive');
    var whatsappLink = document.getElementById('modal-infos-whatsapp');
    var copyBtn = document.getElementById('modal-infos-copy-btn');
    var emailEl = document.getElementById('modal-infos-email');
    var emailWrap = document.getElementById('modal-infos-email-wrap');
    var autresEl = document.getElementById('modal-infos-autres');
    var autresWrap = document.getElementById('modal-infos-autres-wrap');
    var btns = document.querySelectorAll('.js-open-infos-adverse');
    var currentNumero = '';
    function toE164(num) {
        var n = (num || '').replace(/\s/g, '').replace(/^0/, '33');
        if (n && !/^\+?[0-9]+$/.test(n)) n = n.replace(/\D/g, '');
        if (n && n.length <= 9 && n.charAt(0) !== '3') n = '33' + n;
        return n.replace(/^\+/, '');
    }
    function formatNumeroDisplay(num) {
        var n = (num || '').replace(/\D/g, '');
        if (n.length === 10 && (n[0] === '6' || n[0] === '7')) return n.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
        if (n.length === 11 && n.substring(0, 2) === '33') return '+33 ' + n.substring(2).replace(/(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
        return num || '—';
    }
    function setBlockVisibility(wrap, value) {
        if (!wrap) return;
        if (value && String(value).trim() !== '') {
            wrap.style.display = '';
        } else {
            wrap.style.display = 'none';
        }
    }
    function openModal(at, nomAgence, agence, agent, numero, email, autres) {
        currentNumero = (numero || '').replace(/\s/g, '');
        var numE164 = toE164(numero || '');
        atEl.textContent = at ? (at.startsWith('@') ? at : '@' + at) : '—';
        nomAgenceEl.textContent = nomAgence || '—';
        agenceEl.textContent = agence || '—';
        agentEl.textContent = agent || '—';
        numeroEl.textContent = formatNumeroDisplay(numero);
        if (emailEl) {
            emailEl.textContent = email || '—';
            emailEl.href = email ? ('mailto:' + email) : '#';
        }
        if (autresEl) autresEl.textContent = autres && String(autres).trim() ? autres : '—';
        setBlockVisibility(agenceWrap, agence);
        setBlockVisibility(agentWrap, agent);
        setBlockVisibility(emailWrap, email);
        setBlockVisibility(autresWrap, autres);
        if (currentNumero) {
            telActions.classList.remove('hidden');
            telPrive.href = 'tel:' + currentNumero;
            whatsappLink.href = 'https://wa.me/' + numE164;
        } else {
            telActions.classList.add('hidden');
        }
        copyBtn.textContent = 'Copier le numéro';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            if (!currentNumero) return;
            navigator.clipboard.writeText(currentNumero).then(function() {
                copyBtn.textContent = 'Copié !';
                setTimeout(function() { copyBtn.textContent = 'Copier le numéro'; }, 1500);
            });
        });
    }
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
    btns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            openModal(
                btn.getAttribute('data-at') || '',
                btn.getAttribute('data-nom-agence') || '',
                btn.getAttribute('data-agence') || '',
                btn.getAttribute('data-agent') || '',
                btn.getAttribute('data-numero') || '',
                btn.getAttribute('data-email') || '',
                btn.getAttribute('data-autres') || ''
            );
        });
    });
    modal.querySelector('.js-close-modal-infos').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
})();
</script>
@endif

<style>
/* Modale infos adverse */
.modal-infos-adverse-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
}
.modal-infos-adverse-overlay.hidden { display: none !important; }
.modal-infos-adverse-overlay:not(.hidden) { display: flex !important; }
.modal-infos-adverse-box { min-width: 280px; }
@media (min-width: 640px) {
    .modal-infos-adverse-box { min-width: 320px; }
}

/* Vs : dégradé + léger pulse */
.vs-animated {
    display: inline-block;
    font-weight: 700;
    letter-spacing: 0.08em;
    padding: 0 0.2em;
    background: linear-gradient(135deg, #f87171 0%, #fb923c 50%, #fbbf24 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    text-shadow: none;
    /* Contour léger pour rester lisible sur tout fond */
    filter: drop-shadow(0 0 1px rgba(0,0,0,0.8)) drop-shadow(0 0 2px rgba(248, 113, 113, 0.5));
    animation: vsGlow 2.5s ease-in-out infinite;
}
@keyframes vsGlow {
    0%, 100% {
        filter: drop-shadow(0 0 1px rgba(0,0,0,0.8)) drop-shadow(0 0 2px rgba(248, 113, 113, 0.5)) drop-shadow(0 0 8px rgba(251, 146, 60, 0.4));
        transform: scale(1);
    }
    50% {
        filter: drop-shadow(0 0 1px rgba(0,0,0,0.8)) drop-shadow(0 0 4px rgba(248, 113, 113, 0.8)) drop-shadow(0 0 16px rgba(251, 146, 60, 0.6));
        transform: scale(1.06);
    }
}

/* Bouton Demander un match : style BD (inclinaison + ombre) + shimmer */
.ultra-btn-cta.btn-demande-match {
    display: block;
    width: 100%;
    text-align: center;
    border-radius: 0;
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: 1rem 1.5rem;
    font-size: 1rem;
    background: #6225E6;
    box-shadow: 6px 6px 0 #0f172a;
    transform: skewX(-15deg);
    transition: box-shadow 0.3s ease, transform 0.2s ease;
}
.ultra-btn-cta.btn-demande-match:hover {
    box-shadow: 10px 10px 0 #FBC638;
    transform: skewX(-15deg) scale(1.02);
}
.ultra-btn-cta.btn-demande-match:active { transform: skewX(-15deg) scale(0.98); }
.btn-demande-shimmer {
    position: absolute;
    inset: -60px;
    border-radius: inherit;
    background: conic-gradient(from 0deg, transparent 0%, transparent 10%, rgba(255,255,255,0.4) 36%, rgba(255,255,255,0.6) 45%, transparent 50%, transparent 60%, rgba(255,255,255,0.35) 85%, rgba(255,255,255,0.5) 95%, transparent 100%);
    animation: btn-demande-shimmer-spin 2s linear infinite;
    pointer-events: none;
    mix-blend-mode: overlay;
}
.btn-demande-match:hover .btn-demande-shimmer { animation-duration: 1s; }
@keyframes btn-demande-shimmer-spin { to { transform: rotate(360deg); } }
.btn-demande-text { position: relative; z-index: 1; color: #fff; text-shadow: 0 0 20px rgba(255,255,255,0.3); transition: text-shadow 0.3s ease; }
.btn-demande-match:hover .btn-demande-text { text-shadow: 0 0 24px rgba(255,255,255,0.6), 0 0 40px rgba(255,255,255,0.25); }
</style>
@endsection
