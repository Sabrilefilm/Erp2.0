@extends('layouts.app')

@section('title', 'Créateurs')

@section('content')
<div class="space-y-6 pb-8">
    {{-- En-tête épuré : titre + chiffre + action --}}
    <div class="flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[#64748b] text-sm font-medium uppercase tracking-wider">Fiches & équipes</p>
                <h1 class="text-2xl md:text-3xl font-bold text-white mt-1 tracking-tight">Créateurs</h1>
            </div>
            @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}?role=createur" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#1877f2] hover:bg-[#166fe5] text-white font-semibold text-sm transition-colors shrink-0 shadow-lg shadow-[#1877f2]/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Ajouter un créateur
            </a>
            @endcan
        </div>

        {{-- Barre filtre + recherche : une seule ligne élégante --}}
        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-3 flex flex-col sm:flex-row gap-3">
            <form action="{{ route('createurs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 flex-1 min-w-0">
                @if($showEquipeFilter)
                <select name="equipe_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:ring-2 focus:ring-[#1877f2]/50 focus:border-[#1877f2]/50 min-w-[160px]">
                    <option value="">Toutes les équipes</option>
                    @foreach($equipes as $eq)
                    <option value="{{ $eq->id }}" {{ request('equipe_id') == $eq->id ? 'selected' : '' }}>{{ $eq->nom }}</option>
                    @endforeach
                </select>
                @endif
                <div class="flex flex-1 min-w-0 gap-2">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou identifiant…" class="flex-1 min-w-0 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white placeholder-[#64748b] text-sm focus:ring-2 focus:ring-[#1877f2]/50 focus:border-[#1877f2]/50">
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium transition-colors shrink-0 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Rechercher
                    </button>
                </div>
            </form>
            <div class="flex items-center gap-2 sm:border-l sm:border-white/10 sm:pl-4 shrink-0">
                <span class="text-2xl font-bold text-white tabular-nums">{{ number_format($totalCreateurs, 0, ',', ' ') }}</span>
                <span class="text-[#94a3b8] text-sm">créateur{{ $totalCreateurs > 1 ? 's' : '' }}</span>
                @if(request('search'))
                <span class="text-[#64748b] text-xs">· {{ $createurs->total() }} résultat{{ $createurs->total() > 1 ? 's' : '' }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Liste en carte --}}
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5">
                        <th class="text-left px-5 py-3 font-semibold text-white">Nom</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Identifiant</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Téléphone</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Agent</th>
                        <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Manager</th>
                        <th class="px-5 py-3 font-semibold text-right text-white">Fiche</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($createurs as $c)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><span class="font-semibold text-white">{{ $c->name }}</span></td>
                        <td class="px-5 py-3.5 text-[#94a3b8]">{{ $c->username ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-[#94a3b8]">{{ $c->phone ?? $c->email ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-[#94a3b8]">{{ $c->createur?->agent?->name ?? $c->equipe?->agents->first()?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-[#94a3b8]">{{ $c->manager?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-right">
                            @can('update', $c)
                            @if($c->createur)
                            <a href="{{ route('createurs.show', $c->createur) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500/20 text-sky-400 hover:bg-sky-500/30 text-sm font-medium">Voir la fiche</a>
                            @else
                            <a href="{{ route('users.edit', $c) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500/20 text-sky-400 hover:bg-sky-500/30 text-sm font-medium">Modifier (utilisateur)</a>
                            @endif
                            @elseif($c->id === auth()->id())
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500/20 text-sky-400 hover:bg-sky-500/30 text-sm font-medium">Mon profil</a>
                            @else
                            <span class="text-[#64748b] text-sm">—</span>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-[#94a3b8]">Aucun créateur (aucun utilisateur avec le rôle Créateur).</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($createurs->hasPages())
        <div class="px-5 py-4 border-t border-white/10">{{ $createurs->links() }}</div>
        @endif
    </div>
</div>
@endsection
