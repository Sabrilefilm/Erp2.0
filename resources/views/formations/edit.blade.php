@extends('layouts.app')

@section('title', 'Modifier le contenu')

@section('content')
<div class="space-y-6 max-w-xl">
    <a href="{{ route('formations.index') }}" class="text-sm text-[#b0bee3] hover:text-white">← Retour au catalogue</a>
    <h1 class="text-xl font-bold text-white">Modifier le contenu</h1>

    <div class="ultra-card rounded-xl p-5 border border-white/10">
        <form action="{{ route('formations.update', $formation) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Titre</label>
                <input type="text" name="titre" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('titre', $formation->titre) }}" placeholder="Ex. Formation TikTok">
                @error('titre')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Type</label>
                <select name="type" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                    @foreach(\App\Models\Formation::TYPES as $value => $label)
                    <option value="{{ $value }}" {{ old('type', $formation->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Catalogue (thème)</label>
                <select name="catalogue" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                    <option value="">— Aucun —</option>
                    @foreach($catalogues as $cat)
                    <option value="{{ $cat->slug }}" {{ old('catalogue', $formation->catalogue) === $cat->slug ? 'selected' : '' }}>{{ $cat->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Lien (optionnel)</label>
                <input type="url" name="url" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('url', $formation->url) }}" placeholder="https://...">
                @error('url')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Photo ou vidéo (optionnel)</label>
                @if($formation->media_path)
                <p class="text-xs text-emerald-400 mb-1">Fichier actuel : {{ basename($formation->media_path) }}. Choisir un nouveau pour remplacer.</p>
                @endif
                <input type="file" name="media" accept="image/*,video/*" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-neon-blue/30 file:text-white file:text-sm">
                <p class="text-xs text-[#6b7a9f] mt-0.5">Image ou vidéo. Max 10 Go.</p>
                @error('media')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Fichier à télécharger (optionnel)</label>
                @if($formation->fichier_path)
                <p class="text-xs text-emerald-400 mb-1">Actuel : {{ $formation->fichier_nom ?? basename($formation->fichier_path) }}. Choisir un nouveau pour remplacer.</p>
                @endif
                <input type="file" name="fichier" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-neon-blue/30 file:text-white file:text-sm">
                <p class="text-xs text-[#6b7a9f] mt-0.5">PDF, document… Max 10 Go.</p>
                @error('fichier')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Description (optionnel)</label>
                <textarea name="description" rows="3" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm resize-y" placeholder="Courte description">{{ old('description', $formation->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Mots-clés (optionnel)</label>
                <input type="text" name="mots_cles" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('mots_cles', $formation->mots_cles) }}" placeholder="Ex. glycémie, globules rouges, sang, VS">
                <p class="text-xs text-[#6b7a9f] mt-0.5">Utilisés pour générer les questions du quiz (séparés par des virgules).</p>
            </div>
            <div class="flex items-center gap-4 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="actif" value="1" {{ old('actif', $formation->actif) ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-neon-blue">
                    <span class="text-sm text-[#b0bee3]">Visible</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-[#6b7a9f]">Ordre</span>
                    <input type="number" name="ordre" min="0" class="ultra-input w-16 px-2 py-1.5 rounded text-white text-sm" value="{{ old('ordre', $formation->ordre) }}">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-lg text-sm font-medium">Enregistrer</button>
                <a href="{{ route('formations.index') }}" class="px-4 py-2 rounded-lg text-sm text-[#b0bee3] hover:text-white border border-white/10 inline-block">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
