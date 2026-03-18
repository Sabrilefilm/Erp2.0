@extends('layouts.app')

@section('title', 'Modifier le contact adverse')

@section('content')
<div class="space-y-6 pb-8 max-w-xl">
    <a href="{{ route('donnees-match.index') }}" class="inline-flex items-center gap-1.5 text-sm text-[#94a3b8] hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour au répertoire
    </a>

    <h1 class="text-xl font-bold text-white">Modifier le contact adverse</h1>

    @if($errors->any())
    <div class="rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-3">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="ultra-card rounded-xl p-5 border border-white/10">
        <form action="{{ route('donnees-match.update', $adverse->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-[#b0bee3] mb-1">@ TikTok *</label>
                <input type="text" name="tiktok_at" value="{{ old('tiktok_at', $adverse->tiktok_at) }}" required placeholder="username (sans @)" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#b0bee3] mb-1">Téléphone *</label>
                <input type="text" name="telephone" value="{{ old('telephone', $adverse->telephone) }}" required placeholder="06 12 34 56 78" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#b0bee3] mb-1">Agent / e</label>
                <input type="text" name="agent" value="{{ old('agent', $adverse->agent) }}" placeholder="Nom de l'agent" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#b0bee3] mb-1">Agence</label>
                <input type="text" name="agence" value="{{ old('agence', $adverse->agence) }}" placeholder="Agence" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#b0bee3] mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $adverse->email) }}" placeholder="email@exemple.com" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#b0bee3] mb-1">Autres infos</label>
                <textarea name="autres_infos" rows="2" placeholder="Contact, notes…" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10 resize-y">{{ old('autres_infos', $adverse->autres_infos) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold transition-colors">Enregistrer</button>
                <a href="{{ route('donnees-match.index') }}" class="px-4 py-2 rounded-xl border border-white/10 text-[#94a3b8] hover:text-white hover:bg-white/5 text-sm font-medium transition-colors inline-block">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
