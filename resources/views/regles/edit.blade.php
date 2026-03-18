@extends('layouts.app')

@section('title', 'Modifier le message')

@section('content')
<div class="space-y-6 max-w-2xl">
    <h1 class="text-2xl font-bold bg-gradient-to-r from-neon-blue to-neon-purple bg-clip-text text-transparent">💬 Modifier le message</h1>

    <div class="ultra-card rounded-xl p-5 border border-white/10">
        <form action="{{ route('regles.update', $regle) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Titre *</label>
                <input type="text" name="titre" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('titre', $regle->titre) }}" placeholder="Ex. 📢 Rappel : objectifs du mois">
                @error('titre')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Contenu *</label>
                <textarea name="contenu" required rows="10" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm resize-y font-mono" placeholder="Décrivez la règle…">{{ old('contenu', $regle->contenu) }}</textarea>
                <p class="text-[11px] text-white/45 mt-1">Les retours à la ligne sont conservés à l’affichage.</p>
                @error('contenu')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Ordre d'affichage</label>
                    <input type="number" name="ordre" min="0" class="ultra-input w-24 px-3 py-2 rounded-lg text-white text-sm" value="{{ old('ordre', $regle->ordre) }}">
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="actif" value="1" {{ old('actif', $regle->actif) ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-neon-blue focus:ring-neon-blue">
                    <span class="text-sm text-[#b0bee3]">Message visible (publié)</span>
                </label>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-lg text-sm font-semibold"><span>Enregistrer</span></button>
                <a href="{{ route('regles.index') }}" class="ultra-input px-4 py-2 rounded-lg text-sm font-medium text-[#b0bee3] hover:text-white transition-colors inline-block">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
