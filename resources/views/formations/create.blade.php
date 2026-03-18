@extends('layouts.app')

@section('title', 'Ajouter un contenu')

@push('styles')
<style>
    .form-create-hero { animation: form-fade-in 0.4s ease-out; }
    .form-create-card { animation: form-fade-in 0.45s ease-out backwards; }
    .form-create-card:nth-child(1){ animation-delay: 0.05s; }
    .form-create-card:nth-child(2){ animation-delay: 0.1s; }
    .form-create-card:nth-child(3){ animation-delay: 0.15s; }
    @keyframes form-fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .catalogue-row { transition: opacity 0.2s, background 0.2s; }
    .catalogue-row:hover { background: rgba(255,255,255,0.04); }
    .btn-delete-catalogue:hover { background: rgba(239,68,68,0.2); color: #f87171; }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-8 max-w-2xl">
    <a href="{{ route('formations.index') }}" class="inline-flex items-center gap-1.5 text-sm text-[#94a3b8] hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour au catalogue
    </a>

    {{-- Hero --}}
    <div class="form-create-hero rounded-2xl overflow-hidden bg-gradient-to-br from-[#00d4ff]/20 via-cyan-600/10 to-indigo-600/10 border border-[#00d4ff]/25 p-6">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#00d4ff]/20 border border-[#00d4ff]/30 flex items-center justify-center shrink-0">
                <span class="text-3xl">📚</span>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl font-bold text-white">Nouveau contenu</h1>
                <p class="text-[#94a3b8] text-sm mt-1">Ajoutez un module au catalogue (vidéo, document ou lien) et assignez-lui un thème.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm px-4 py-3">{{ session('success') }}</div>
    @endif
    @if($errors->has('catalogue_label'))
    <div class="rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-3">{{ $errors->first('catalogue_label') }}</div>
    @endif

    {{-- 1. Gérer les catalogues (thèmes) --}}
    <div class="form-create-card rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
        <div class="px-5 py-4 border-b border-white/10 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs font-bold">1</span>
            <h2 class="text-sm font-semibold text-white">Catalogues (thèmes)</h2>
        </div>
        <div class="p-5 space-y-4">
            <p class="text-xs text-[#94a3b8]">Les contenus peuvent être rattachés à un thème. Ajoutez des catalogues ici ou supprimez ceux qui ne servent plus.</p>

            {{-- Liste des catalogues + supprimer --}}
            <ul class="space-y-1">
                @forelse($catalogues as $cat)
                <li class="catalogue-row flex items-center justify-between gap-3 py-2 px-3 rounded-lg">
                    <span class="text-sm text-white font-medium">{{ $cat->label }}</span>
                    <form action="{{ route('formations.catalogues.destroy', $cat->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Supprimer ce catalogue ? Les contenus concernés n\'auront plus de thème.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete-catalogue inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium text-[#94a3b8] hover:border border-transparent border-red-500/30 transition-colors" title="Supprimer ce catalogue">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Supprimer
                        </button>
                    </form>
                </li>
                @empty
                <li class="text-sm text-[#64748b] py-2">Aucun catalogue. Ajoutez-en un ci-dessous.</li>
                @endforelse
            </ul>

            {{-- Ajouter un catalogue --}}
            <form action="{{ route('formations.catalogues.store') }}" method="POST" class="flex flex-wrap items-end gap-3 pt-2 border-t border-white/10">
                @csrf
                <div class="min-w-[200px] flex-1">
                    <label for="catalogue_label" class="block text-xs font-medium text-[#94a3b8] mb-1">Nouveau catalogue</label>
                    <input type="text" name="label" id="catalogue_label" required maxlength="255" placeholder="Ex. Découvrir TikTok"
                           class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30"
                           value="{{ old('label') }}">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition-colors shrink-0">
                    Ajouter
                </button>
            </form>
        </div>
    </div>

    {{-- 2. Nouveau contenu (formulaire) --}}
    <div class="form-create-card rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
        <div class="px-5 py-4 border-b border-white/10 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-[#00d4ff]/20 text-[#00d4ff] flex items-center justify-center text-xs font-bold">2</span>
            <h2 class="text-sm font-semibold text-white">Détails du contenu</h2>
        </div>
        <div class="p-5">
            <form action="{{ route('formations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Titre *</label>
                    <input type="text" name="titre" required class="ultra-input w-full px-3 py-2.5 rounded-xl text-white text-sm border border-white/10 focus:border-[#00d4ff]/50 focus:ring-1 focus:ring-[#00d4ff]/30" value="{{ old('titre') }}" placeholder="Ex. Formation TikTok">
                    @error('titre')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#b0bee3] mb-1">Type</label>
                        <select name="type" class="ultra-input w-full px-3 py-2.5 rounded-xl text-white text-sm border border-white/10">
                            @foreach(\App\Models\Formation::TYPES as $value => $label)
                            <option value="{{ $value }}" {{ old('type', 'video') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#b0bee3] mb-1">Catalogue (thème)</label>
                        <select name="catalogue" class="ultra-input w-full px-3 py-2.5 rounded-xl text-white text-sm border border-white/10">
                            <option value="">— Aucun —</option>
                            @foreach($catalogues as $cat)
                            <option value="{{ $cat->slug }}" {{ old('catalogue') === $cat->slug ? 'selected' : '' }}>{{ $cat->label }}</option>
                            @endforeach
                        </select>
                        @error('catalogue')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Lien (optionnel)</label>
                    <input type="url" name="url" class="ultra-input w-full px-3 py-2.5 rounded-xl text-white text-sm border border-white/10" value="{{ old('url') }}" placeholder="https://...">
                    @error('url')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Photo ou vidéo (optionnel)</label>
                    <input type="file" name="media" accept="image/*,video/*" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-[#00d4ff]/20 file:text-white file:text-sm">
                    <p class="text-xs text-[#64748b] mt-0.5">Image ou vidéo. Max 10 Go.</p>
                    @error('media')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Fichier à télécharger (optionnel)</label>
                    <input type="file" name="fichier" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-[#00d4ff]/20 file:text-white file:text-sm">
                    <p class="text-xs text-[#64748b] mt-0.5">PDF, document… Max 10 Go.</p>
                    @error('fichier')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Description (optionnel)</label>
                    <textarea name="description" rows="3" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm resize-y border border-white/10" placeholder="Courte description">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Mots-clés (optionnel)</label>
                    <input type="text" name="mots_cles" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10" value="{{ old('mots_cles') }}" placeholder="Ex. glycémie, globules rouges">
                    <p class="text-xs text-[#64748b] mt-0.5">Pour la génération des questions du quiz.</p>
                </div>
                <div class="flex flex-wrap items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="actif" value="1" {{ old('actif', true) ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-[#00d4ff]">
                        <span class="text-sm text-[#b0bee3]">Visible dans le catalogue</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-[#94a3b8]">Ordre</label>
                        <input type="number" name="ordre" min="0" class="ultra-input w-20 px-2 py-1.5 rounded-lg text-white text-sm border border-white/10" value="{{ old('ordre', 0) }}">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 pt-4 border-t border-white/10">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#00d4ff] hover:bg-[#00b8e6] text-[#0a0e27] text-sm font-semibold transition-colors">
                        Enregistrer le contenu
                    </button>
                    <a href="{{ route('formations.index') }}" class="px-4 py-2.5 rounded-xl text-sm text-[#94a3b8] hover:text-white border border-white/10 hover:bg-white/5 transition-colors inline-block">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
