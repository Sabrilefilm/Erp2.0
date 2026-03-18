@extends('layouts.app')

@section('title', 'Mes créateurs')

@section('content')
<div class="space-y-6 pb-8">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e3a8a] to-[#1e40af] border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-tight">Mes créateurs</h1>
                <p class="text-blue-200/90 text-sm mt-1">Heures, jours, diamants (données import Excel, pas inventées) et demandes — créateurs de votre liste</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-white/90 hover:text-white text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Tableau de bord
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5">
                        <th class="text-left px-5 py-3 font-semibold text-white">Créateur</th>
                        <th class="text-right px-5 py-3 font-semibold text-[#94a3b8]">Heures</th>
                        <th class="text-right px-5 py-3 font-semibold text-[#94a3b8]">Jours</th>
                        <th class="text-right px-5 py-3 font-semibold text-[#94a3b8]">Diamants</th>
                        <th class="text-center px-5 py-3 font-semibold text-[#94a3b8]">Ton analyse</th>
                        <th class="text-center px-5 py-3 font-semibold text-[#94a3b8]">Demandes</th>
                        <th class="px-5 py-3 font-semibold text-right text-white">Fiche</th>
                    </tr>
                </thead>
                <tbody>
                    @php $objectifJours = 7; $objectifHeures = 16; @endphp
                    @forelse($createurs as $c)
                    @php
                        $j = $c->jours_mois !== null ? (float) $c->jours_mois : null;
                        $h = $c->heures_mois !== null ? (float) $c->heures_mois : null;
                        $hasData = $j !== null || $h !== null;
                        $pctJ = ($objectifJours > 0 && $j !== null) ? min(100, (int) round(($j / $objectifJours) * 100)) : 0;
                        $pctH = ($objectifHeures > 0 && $h !== null) ? min(100, (int) round(($h / $objectifHeures) * 100)) : 0;
                        $pctC = $hasData ? (int) round(($pctJ + $pctH) / 2) : null;
                        $analyseColor = $pctC === null ? '#64748b' : ($pctC < 30 ? '#ef4444' : ($pctC < 50 ? '#f59e0b' : '#e2e8f0'));
                        $analyseEmoji = $pctC === null ? '—' : ($pctC < 30 ? '🔴' : ($pctC < 50 ? '🟠' : '✅'));
                    @endphp
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-semibold text-white">{{ $c->nom ?: ($c->user?->name ?? '—') }}</span>
                            @if($c->pseudo_tiktok)
                            <span class="block text-xs text-[#94a3b8] mt-0.5">{{ $c->pseudo_tiktok }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right text-white">{{ $c->heures_mois !== null ? \App\Support\HeuresHelper::format((float) $c->heures_mois) : '—' }}</td>
                        <td class="px-5 py-3.5 text-right text-white">{{ $c->jours_mois !== null ? (int) $c->jours_mois : '—' }}</td>
                        <td class="px-5 py-3.5 text-right text-white">{{ $c->diamants !== null ? number_format($c->diamants, 0, ',', ' ') : '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($pctC !== null)
                            <span class="inline-flex items-center justify-center gap-1 font-semibold tabular-nums" style="color: {{ $analyseColor }}"><span aria-hidden="true">{{ $analyseEmoji }}</span>{{ $pctC }}%</span>
                            @else
                            <span class="text-[#64748b]">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if(($c->demandes_en_attente ?? 0) > 0)
                            <span class="inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-xs font-semibold">{{ $c->demandes_en_attente }}</span>
                            @else
                            <span class="text-[#64748b]">0</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('createurs.show', $c) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500/20 text-sky-400 hover:bg-sky-500/30 text-sm font-medium">Voir la fiche</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-[#94a3b8]">Aucun créateur dans votre liste.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
