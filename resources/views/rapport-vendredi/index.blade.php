@extends('layouts.app')

@section('title', 'Rapport de la semaine')

@section('content')
<div class="space-y-6 pb-8 max-w-3xl mx-auto">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-amber-500/20 via-orange-500/10 to-rose-500/10 border border-white/10 p-6 md:p-8">
        <h1 class="text-2xl md:text-3xl font-bold text-white">Rapport de la semaine</h1>
        <p class="text-[#94a3b8] text-sm mt-2">Obligatoire chaque semaine. Il permet à la direction de vous accompagner, de vous donner des consignes adaptées et de trouver des solutions (ex. difficultés à trouver des matchs → contacter d'autres agences ou votre manageur).</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ {{ session('success') }}</div>
    @endif

    @if(!$rapportSemaineCourante)
    <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 mb-6">
        <p class="text-amber-200 text-sm font-medium">Rapport de la semaine en attente — merci de le remplir pour garder la traçabilité et permettre un accompagnement.</p>
    </div>
    <div class="ultra-card rounded-xl p-6 border border-white/10">
        <h2 class="text-lg font-semibold text-white mb-4">Semaine du {{ \Carbon\Carbon::now()->setISODate($annee, $semaine)->startOfWeek()->format('d/m/Y') }}</h2>
        <form action="{{ route('rapport-vendredi.store') }}" method="POST">
            @csrf
            <div>
                <label for="contenu" class="block text-sm font-medium text-[#b0bee3] mb-2">Votre rapport (points bloquants, difficultés, besoins, remarques…)</label>
                <textarea name="contenu" id="contenu" rows="8" required class="ultra-input w-full px-3 py-2 rounded-xl text-white resize-y" placeholder="Ex. : J'ai du mal à gérer mon équipe car je prends les matchs trop tard. J'aurais besoin de conseils pour anticiper.">{{ old('contenu') }}</textarea>
                @error('contenu')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="mt-4">
                <button type="submit" class="ultra-btn-primary px-5 py-2.5 rounded-xl font-semibold text-sm">Enregistrer le rapport</button>
            </div>
        </form>
    </div>
    @else
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">Vous avez déjà rempli le rapport de la semaine.</div>
    <div class="ultra-card rounded-xl p-6 border border-white/10">
        <h2 class="text-lg font-semibold text-white mb-2">Votre rapport</h2>
        <div class="text-[#b0bee3] whitespace-pre-wrap">{{ $rapportSemaineCourante->contenu }}</div>
        <p class="text-xs text-[#64748b] mt-3">Enregistré le {{ $rapportSemaineCourante->created_at->translatedFormat('d/m/Y a H:i') }}</p>
    </div>
    @endif

    @if($mesRapports->isNotEmpty())
    <div class="ultra-card rounded-xl p-6 border border-white/10">
        <h2 class="text-lg font-semibold text-white mb-4">Vos rapports précédents</h2>
        <ul class="space-y-4">
            @foreach($mesRapports as $r)
            <li class="border-b border-white/5 pb-4 last:border-0 last:pb-0">
                <p class="text-xs text-[#94a3b8]">{{ $r->libelle_semaine }} — {{ $r->created_at->translatedFormat('d/m/Y H:i') }}</p>
                <p class="text-[#b0bee3] text-sm mt-1 line-clamp-3">{{ Str::limit($r->contenu, 200) }}</p>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection
