@extends('layouts.app')

@section('title', $formation->titre)

@section('content')
@php
    $typeLabel = \App\Models\Formation::TYPES[$formation->type] ?? $formation->type;
    $youtubeId = $formation->url ? \App\Models\Formation::youtubeIdFromUrl($formation->url) : null;
    $isVideo = $formation->type === 'video' && $youtubeId;
@endphp
<div class="space-y-6 pb-8 max-w-2xl mx-auto">
    {{-- Retour + titre du module --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('formations.index') }}" class="p-2 -ml-2 rounded-xl text-[#94a3b8] hover:text-white hover:bg-white/10 transition-colors shrink-0" title="Retour au catalogue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-[#94a3b8] uppercase tracking-wider">{{ $typeLabel }}</p>
            <h1 class="text-xl md:text-2xl font-bold text-white truncate">{{ $formation->titre }}</h1>
        </div>
    </div>

    {{-- Carte résumé du module (design cohérent dark) --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden p-6">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-neon-blue/20 to-neon-purple/20 border border-white/10 flex items-center justify-center shrink-0">
                @if($formation->type === 'video')
                <svg class="w-8 h-8 text-neon-blue" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                @elseif($formation->type === 'document')
                <svg class="w-8 h-8 text-neon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                @else
                <svg class="w-8 h-8 text-neon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                @if($formation->description)
                <p class="text-sm text-[#94a3b8] line-clamp-3">{{ $formation->description }}</p>
                @else
                <p class="text-sm text-[#94a3b8]">Consultez le contenu puis validez vos acquis avec le quiz.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Bouton principal : Accéder au contenu --}}
    <a href="{{ route('formations.contenu', $formation) }}" class="block w-full py-4 rounded-2xl bg-neon-blue hover:bg-neon-blue/90 text-[#0f172a] font-bold text-center transition-colors shadow-lg shadow-neon-blue/20">
        Accéder au contenu
    </a>

    {{-- Fichier à télécharger (visible dès la page formation) --}}
    @if($formation->fichier_path)
    <a href="{{ route('formations.fichier', $formation) }}" class="flex items-center justify-center gap-3 w-full py-3.5 rounded-2xl bg-[#0ea5e9] hover:bg-[#0284c7] text-white font-semibold transition-colors border border-cyan-400/30 shadow-lg shadow-cyan-500/20">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Télécharger le document {{ $formation->fichier_nom ? "« ".e($formation->fichier_nom)." »" : '' }}
    </a>
    @endif

    {{-- Quiz --}}
    @if($formation->questions()->exists())
    <a href="{{ route('formations.quiz.show', $formation) }}" class="block w-full py-3.5 rounded-2xl border-2 border-neon-purple/50 bg-neon-purple/10 hover:bg-neon-purple/20 text-neon-purple font-semibold text-center transition-colors">
        Passer le quiz final
    </a>
    @else
    <p class="text-center text-sm text-[#64748b] py-2">Quiz non disponible pour ce module.</p>
    @endif

    {{-- Actions admin --}}
    @if(auth()->user()->canAddEntries())
    <div class="flex flex-wrap items-center gap-3 pt-6 border-t border-white/10">
        @if(config('services.openai.api_key'))
        <form action="{{ route('formations.quiz.generate', $formation) }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="difficulte" value="moyen">
            <input type="hidden" name="count" value="8">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-neon-blue/20 hover:bg-neon-blue/30 text-neon-blue text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                Générer les questions du quiz
            </button>
        </form>
        @endif
        <a href="{{ route('formations.edit', $formation) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-[#94a3b8] hover:text-white text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
        </a>
        <form action="{{ route('formations.destroy', $formation) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce contenu ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-red-500/20 text-[#94a3b8] hover:text-red-400 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Supprimer
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
