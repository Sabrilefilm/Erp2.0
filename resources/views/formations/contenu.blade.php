@extends('layouts.app')

@section('title', $formation->titre)

@push('styles')
<style>
.contenu-hero {
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    min-height: 180px;
    display: flex;
    align-items: flex-end;
}
.contenu-prose p, .contenu-prose li { line-height: 1.8; }
.contenu-prose { color: #b0bee3; }
</style>
@endpush

@section('content')
@php
    $youtubeId = $formation->url ? \App\Models\Formation::youtubeIdFromUrl($formation->url) : null;
    $isVideo   = $formation->type === 'video' && $youtubeId;
    $paragraphs = $formation->description ? array_values(array_filter(preg_split('/\r\n\r\n|\r\r|\n\n/', $formation->description))) : [];
    $lines      = $formation->description ? array_values(array_filter(preg_split('/\r\n|\r|\n/', $formation->description))) : [];
    $catalogueLabel = $formation->catalogue ? (\App\Models\Formation::CATALOGUES[$formation->catalogue] ?? null) : null;
    $gradients = ['video'=>'linear-gradient(135deg,#b91c1c,#ef4444)','document'=>'linear-gradient(135deg,#1d4ed8,#3b82f6)','lien'=>'linear-gradient(135deg,#065f46,#10b981)'];
    $grad = $gradients[$formation->type] ?? 'linear-gradient(135deg,#374151,#6b7280)';
    $hasQuiz = $formation->questions()->exists();
@endphp
<div class="max-w-3xl mx-auto pb-12 pt-2">

    {{-- Retour --}}
    <a href="{{ route('formations.index') }}" class="inline-flex items-center gap-2 text-sm text-[#94a3b8] hover:text-white transition-colors mb-6 group">
        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour au catalogue
    </a>

    {{-- Banneau coloré (même style que la carte) --}}
    <div class="contenu-hero mb-6" style="background: {{ $grad }};">
        @if($youtubeId)
        <img src="https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg" alt="" class="absolute inset-0 w-full h-full object-cover opacity-30">
        @endif
        <div class="relative z-10 p-7 w-full">
            <p class="text-[10px] font-bold uppercase tracking-widest text-white/75 mb-2">{{ $catalogueLabel ?: ($formation->type === 'video' ? 'Cours vidéo' : ($formation->type === 'document' ? 'Formation écrite' : 'Ressource externe')) }}</p>
            <h1 class="text-2xl md:text-3xl font-bold text-white leading-snug">{{ $formation->titre }}</h1>
            <div class="flex items-center gap-3 mt-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/80 bg-white/15 backdrop-blur rounded-lg px-3 py-1.5">
                    @if($formation->type === 'document')
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Formation écrite
                    @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Ressource externe
                    @endif
                </span>
                <span class="text-xs text-white/60 font-semibold bg-white/10 rounded-lg px-3 py-1.5">Gratuit</span>
                @if($hasQuiz)
                <span class="text-xs text-white/60 font-semibold bg-white/10 rounded-lg px-3 py-1.5">Quiz disponible</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Photo ou vidéo uploadée --}}
    @if($formation->media_path)
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] overflow-hidden mb-6">
        @if($formation->isMediaImage())
        <img src="{{ route('formations.media', $formation) }}" alt="" class="w-full max-h-[70vh] object-contain bg-black/20">
        @elseif($formation->isMediaVideo())
        <video src="{{ route('formations.media', $formation) }}" controls class="w-full max-h-[70vh] bg-black/20">
            Votre navigateur ne prend pas en charge la lecture de la vidéo.
        </video>
        @else
        <a href="{{ route('formations.media', $formation) }}" target="_blank" rel="noopener" class="block p-6 text-center text-[#00d4ff] hover:underline">Ouvrir le média</a>
        @endif
    </div>
    @endif

    {{-- Fichier à télécharger --}}
    @if($formation->fichier_path)
    <div class="rounded-2xl border border-[#0ea5e9]/40 bg-[#0e7490]/25 p-4 mb-6">
        <a href="{{ route('formations.fichier', $formation) }}" class="inline-flex items-center gap-3 px-5 py-3 rounded-xl bg-[#0ea5e9] hover:bg-[#0284c7] text-white font-semibold shadow-lg shadow-cyan-500/20 transition-colors border border-cyan-400/30">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Télécharger le fichier {{ $formation->fichier_nom ? "« {$formation->fichier_nom} »" : '' }}
        </a>
    </div>
    @endif

    {{-- Contenu principal : texte --}}
    @if(count($lines) > 0)
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 md:p-8 mb-6">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-[#94a3b8] mb-5">Contenu du module</h2>
        <div class="contenu-prose space-y-4 text-sm md:text-base">
            @if(count($paragraphs) > 1)
                @foreach($paragraphs as $para)
                <p>{{ $para }}</p>
                @endforeach
            @else
                <ul class="space-y-3 list-none pl-0">
                    @foreach($lines as $line)
                    <li class="flex gap-3 items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-white/35 shrink-0 mt-2.5"></span>
                        <span class="text-white/85">{{ $line }}</span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    @elseif(!$formation->url)
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-8 mb-6 text-center">
        <p class="text-[#94a3b8] text-sm">Aucun contenu texte disponible pour ce module.</p>
    </div>
    @endif

    {{-- Bouton ouvrir lien externe (si pas vidéo et URL) --}}
    @if($formation->url && !$isVideo)
    <a href="{{ $formation->url }}" target="_blank" rel="noopener"
       class="flex items-center justify-center gap-3 w-full py-4 rounded-2xl mb-4 font-semibold text-[#0a0e27] transition-all shadow-lg"
       style="background: {{ $grad }};">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        Ouvrir la ressource
    </a>
    @endif

    {{-- Séparateur quiz --}}
    @if($hasQuiz)
    <div class="rounded-2xl border border-[#b794f6]/30 bg-[#b794f6]/10 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <p class="text-sm font-semibold text-white">Mini quiz disponible ✅</p>
            <p class="text-xs text-[#94a3b8] mt-0.5">Testez vos connaissances sur ce module pour valider vos acquis.</p>
        </div>
        <a href="{{ route('formations.quiz.show', $formation) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#b794f6] hover:bg-[#b794f6]/90 text-[#0a0e27] font-bold text-sm transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Passer le quiz
        </a>
    </div>
    @else
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-center">
        <p class="text-xs text-[#64748b]">Aucun quiz disponible pour ce module pour l'instant.</p>
    </div>
    @endif

    {{-- Module suivant --}}
    @if($nextFormation)
    <div class="mt-6 pt-6 border-t border-white/10">
        <a href="{{ route('formations.contenu', $nextFormation) }}" class="flex items-center justify-between gap-4 rounded-xl border border-white/10 bg-white/5 hover:bg-white/[0.07] p-4 transition-all group">
            <div class="min-w-0">
                <p class="text-[10px] text-[#94a3b8] uppercase tracking-wider mb-1">Module suivant</p>
                <p class="text-sm font-semibold text-white truncate group-hover:text-[#00d4ff] transition-colors">{{ $nextFormation->titre }}</p>
            </div>
            <svg class="w-5 h-5 text-[#94a3b8] group-hover:text-[#00d4ff] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @endif

    {{-- Actions agents (secondaires, pas de bouton bleu principal) --}}
    @if(auth()->user()->canAddEntries())
    <div class="mt-8 pt-6 border-t border-white/10">
        <p class="text-[10px] font-bold uppercase tracking-wider text-white/35 mb-3">Administration du module</p>
        <div class="flex flex-wrap items-center gap-3">
            @if(config('services.openai.api_key'))
            <form action="{{ route('formations.quiz.generate', $formation) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="difficulte" value="moyen">
                <input type="hidden" name="count" value="8">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/15 text-[#94a3b8] hover:text-white hover:border-white/25 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Générer le quiz (IA)
                </button>
            </form>
            @endif
            <a href="{{ route('formations.edit', $formation) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/15 text-[#94a3b8] hover:text-white hover:border-white/25 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </a>
            <form action="{{ route('formations.destroy', $formation) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce module ?');">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/10 text-[#94a3b8] hover:text-red-400 hover:border-red-500/30 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
