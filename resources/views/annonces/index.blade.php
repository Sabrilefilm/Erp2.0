@extends('layouts.app')

@section('title', 'Annonces & Campagnes TikTok')

@push('styles')
<style>
/* Style moderne pour les annonces */
.annonce-card {
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.08);
    background: linear-gradient(165deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100%);
    overflow: hidden;
    transition: all 0.3s ease;
}
.annonce-card:hover {
    border-color: rgba(255,255,255,0.12);
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    transform: translateY(-2px);
}
.annonce-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.annonce-type-annonce {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}
.annonce-type-evenement {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}
.annonce-type-campagne {
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: white;
}
.annonce-details {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    padding: 1rem;
    margin-top: 1rem;
}
.annonce-detail-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}
.annonce-detail-item:last-child {
    margin-bottom: 0;
}
.annonce-detail-icon {
    width: 1.25rem;
    height: 1.25rem;
    opacity: 0.7;
}
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.filter-tab {
    padding: 0.625rem 1.25rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.02);
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}
.filter-tab:hover {
    background: rgba(255,255,255,0.05);
    color: white;
}
.filter-tab.active {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border-color: transparent;
}
</style>
@endpush

@section('content')
<div class="space-y-6 pb-8 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-500/25 via-violet-500/15 to-purple-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-3xl">📢</div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Annonces & Campagnes TikTok</h1>
                    <p class="text-[#94a3b8] text-sm mt-1">Événements, campagnes et annonces de l'agence 🚀</p>
                </div>
            </div>
            @if(auth()->user()->canAddEntries())
            <a href="{{ route('annonces.create') }}" class="ultra-btn-cta shrink-0"><span>+ Nouvelle annonce</span></a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ {{ session('success') }}</div>
    @endif

    {{-- Filtres par type --}}
    <div class="filter-tabs">
        <a href="{{ route('annonces.index', ['type' => 'all']) }}" class="filter-tab {{ $type === 'all' ? 'active' : '' }}">
            📋 Tout voir
        </a>
        <a href="{{ route('annonces.index', ['type' => 'annonce']) }}" class="filter-tab {{ $type === 'annonce' ? 'active' : '' }}">
            📢 Annonces
        </a>
        <a href="{{ route('annonces.index', ['type' => 'evenement']) }}" class="filter-tab {{ $type === 'evenement' ? 'active' : '' }}">
            🎉 Événements
        </a>
        <a href="{{ route('annonces.index', ['type' => 'campagne']) }}" class="filter-tab {{ $type === 'campagne' ? 'active' : '' }}">
            🎯 Campagnes
        </a>
    </div>

    @if($annonces->isEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center">
        <div class="w-20 h-20 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4 text-4xl">📭</div>
        <p class="text-[#94a3b8] text-lg">Aucune annonce pour le moment.</p>
        <p class="text-[#64748b] text-sm mt-1">L'agence pourra poster des annonces et campagnes ici.</p>
        @if(auth()->user()->canAddEntries())
        <a href="{{ route('annonces.create') }}" class="inline-block mt-6 px-5 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white font-semibold text-sm">+ Nouvelle annonce</a>
        @endif
    </div>
    @else
    <div class="space-y-6">
        @foreach($annonces as $annonce)
        <article class="annonce-card p-6 md:p-8 {{ $annonce->actif ? '' : 'opacity-60' }}">
            <header class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="annonce-type-badge annonce-type-{{ $annonce->type }}">
                            @if($annonce->type === 'annonce')📢@elseif($annonce->type === 'evenement')🎉@else🎯@endif
                            {{ $annonce->type_label }}
                        </span>
                        @if(!$annonce->actif)
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-medium bg-white/10 text-[#94a3b8] border border-white/10">Brouillon</span>
                        @endif
                    </div>
                    <h2 class="text-xl md:text-2xl font-bold text-white mb-2">{{ $annonce->titre }}</h2>
                    <p class="text-[#94a3b8] text-sm">📅 {{ $annonce->updated_at->translatedFormat('d F Y \à H:i') }}</p>
                </div>
                @if(auth()->user()->canAddEntries())
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('annonces.edit', $annonce) }}" class="px-3 py-1.5 rounded-lg bg-indigo-500/20 text-indigo-400 hover:bg-indigo-500/30 text-sm font-medium">Modifier</a>
                    <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette annonce ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 text-sm font-medium">Supprimer</button>
                    </form>
                </div>
                @endif
            </header>
            
            <div class="text-white/90 leading-relaxed mb-4">
                {!! nl2br(e($annonce->contenu)) !!}
            </div>

            {{-- Détails spécifiques selon le type --}}
            @if($annonce->type === 'evenement' && ($annonce->date_evenement || $annonce->lieu_evenement))
            <div class="annonce-details">
                @if($annonce->date_evenement)
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-white/80">{{ $annonce->date_evenement->translatedFormat('d F Y \à H:i') }}</span>
                </div>
                @endif
                @if($annonce->lieu_evenement)
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-white/80">{{ $annonce->lieu_evenement }}</span>
                </div>
                @endif
            </div>
            @endif

            @if($annonce->type === 'campagne' && ($annonce->hashtag_principal || $annonce->date_debut || $annonce->date_fin || $annonce->objectif_campagne))
            <div class="annonce-details">
                @if($annonce->hashtag_principal)
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    <span class="text-white/80">#{{ $annonce->hashtag_principal }}</span>
                </div>
                @endif
                @if($annonce->date_debut && $annonce->date_fin)
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-white/80">Du {{ $annonce->date_debut->translatedFormat('d F Y') }} au {{ $annonce->date_fin->translatedFormat('d F Y') }}</span>
                </div>
                @endif
                @if($annonce->objectif_campagne)
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-white/80">{{ $annonce->objectif_campagne }}</span>
                </div>
                @endif
                @if($annonce->lien_tiktok)
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <a href="{{ $annonce->lien_tiktok }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 underline">Voir sur TikTok</a>
                </div>
                @endif
            </div>
            @endif
        </article>
        @endforeach
    </div>
    @endif
</div>
@endsection
