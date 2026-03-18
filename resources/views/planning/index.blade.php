@extends('layouts.app')

@section('title', 'Planning')

@php
    $typeLabels = [
        'match_off' => 'Off',
        'match_anniversaire' => 'Anniversaire',
        'match_depannage' => 'Dépannage',
        'match_tournoi' => 'Tournoi',
        'match_agence' => 'Agence',
    ];
    $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
@endphp

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold bg-gradient-to-r from-neon-blue to-neon-purple bg-clip-text text-transparent">Planning</h1>

    {{-- Filtre par période --}}
    <div class="ultra-card rounded-xl p-4 border border-white/10">
        <h2 class="text-sm font-semibold text-[#b0bee3] mb-3">Période</h2>
        <form action="{{ route('planning.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Du</label>
                <input type="date" name="from" value="{{ $from }}" class="ultra-input px-3 py-2 rounded-lg text-white text-sm w-full min-w-[140px]">
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Au</label>
                <input type="date" name="to" value="{{ $to }}" class="ultra-input px-3 py-2 rounded-lg text-white text-sm w-full min-w-[140px]">
            </div>
            <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-lg text-sm font-semibold"><span>Filtrer</span></button>
        </form>
    </div>

    {{-- Liste des événements par date --}}
    <div class="ultra-card rounded-xl overflow-hidden border border-white/10">
        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between gap-3 flex-wrap">
            <h2 class="text-sm font-semibold text-white">Événements</h2>
            @if(auth()->user()->hasRoleOrAbove('agent'))
<button type="button" onclick="document.getElementById('planning-modal').classList.remove('hidden')" class="ultra-btn-primary px-3 py-1.5 rounded-lg text-sm font-semibold whitespace-nowrap"><span>+ Ajouter un événement</span></button>
            @endif
        </div>

        @if($planningByDate->isEmpty())
        <div class="p-8 text-center text-[#b0bee3]">Aucun événement sur cette période.</div>
        @else
        <div class="divide-y divide-white/5">
            @foreach($planningByDate as $dateStr => $events)
            @php
                $first = $events->first();
                $d = $first->date;
                $dateFormatted = $jours[$d->dayOfWeek] . ' ' . $d->format('d/m/Y');
            @endphp
            <div class="p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-semibold text-neon-blue uppercase tracking-wide">{{ $dateFormatted }}</span>
                    <span class="text-xs text-[#6b7a9f]">({{ $events->count() }} événement{{ $events->count() > 1 ? 's' : '' }})</span>
                </div>
                <ul class="space-y-2">
                    @foreach($events as $p)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2 px-3 rounded-lg bg-white/5 border border-white/5 hover:border-white/10 transition-colors">
                        <div class="flex flex-wrap items-center gap-3 min-w-0">
                            @if($p->heure)
                            <span class="text-xs font-medium text-[#b0bee3] shrink-0">{{ substr($p->heure, 0, 5) }}</span>
                            @endif
                            <span class="font-medium text-white shrink-0">{{ $p->createur->nom }}</span>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-neon-blue/20 text-neon-blue shrink-0">{{ $typeLabels[$p->type] ?? $p->type }}</span>
                            @if($p->createur_adverse)
                            <span class="text-sm text-[#b0bee3] shrink-0">vs {{ $p->createur_adverse }}</span>
                            @endif
                            @if($p->raison)
                            <span class="text-sm text-[#b0bee3] truncate">{{ $p->raison }}</span>
                            @endif
                        </div>
                        @if(auth()->user()->hasRoleOrAbove('agent'))
                        <form action="{{ route('planning.destroy', $p) }}" method="POST" class="inline shrink-0" onsubmit="return confirm('Supprimer cet événement ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-neon-pink hover:underline">Supprimer</button>
                        </form>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@if(auth()->user()->hasRoleOrAbove('agent'))
{{-- Modal Ajouter un événement --}}
<div id="planning-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('planning-modal').classList.add('hidden')" aria-label="Fermer"></div>
    <div class="relative ultra-card rounded-xl border border-white/10 w-full max-w-md p-5 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Ajouter un événement</h3>
            <button type="button" onclick="document.getElementById('planning-modal').classList.add('hidden')" class="p-2 text-[#b0bee3] hover:text-white rounded-lg hover:bg-white/10 transition-colors" aria-label="Fermer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('planning.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Créateur</label>
                <select name="createur_id" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                    <option value="">—</option>
                    @foreach($createurs as $c)
                    <option value="{{ $c->id }}">{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Date</label>
                    <input type="date" name="date" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Heure</label>
                    <input type="time" name="heure" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" placeholder="Optionnel">
                </div>
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Type</label>
                <select name="type" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                    @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Créateur adverse (estimation du match)</label>
                <input type="text" name="createur_adverse" placeholder="Nom ou @ du créateur adverse" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Raison</label>
                <input type="text" name="raison" placeholder="Optionnel" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="ultra-btn-primary flex-1 px-4 py-2 rounded-lg text-sm font-semibold"><span>Ajouter</span></button>
                <button type="button" onclick="document.getElementById('planning-modal').classList.add('hidden')" class="ultra-input px-4 py-2 rounded-lg text-sm font-medium text-[#b0bee3] hover:text-white transition-colors">Annuler</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
