@extends('layouts.app')

@section('title', 'Modifier l\'agence')

@section('content')
<div class="space-y-6 pb-8 max-w-xl">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e3a8a] to-[#1e40af] border border-white/10 p-6">
        <h1 class="text-2xl font-bold text-white">Modifier l'agence</h1>
        <p class="text-blue-200/90 text-sm mt-1">{{ $equipe->nom }}</p>
    </div>

    <form action="{{ route('equipes.update', $equipe) }}" method="POST" class="rounded-2xl border border-white/10 bg-white/[0.02] p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="nom" class="block text-sm font-medium text-[#94a3b8] mb-1">Nom de l'agence *</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom', $equipe->nom) }}" required maxlength="255"
                   class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white placeholder-[#64748b] focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
            @error('nom')
                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="manager_id" class="block text-sm font-medium text-[#94a3b8] mb-1">Agent ou Manageur</label>
            <select name="manager_id" id="manager_id" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                <option value="">— Aucun —</option>
                @foreach($agentsEtManagers as $u)
                    <option value="{{ $u->id }}" {{ old('manager_id', $equipe->manager_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->getRoleLabel() }})</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-[#64748b]">Responsable de l'agence : agent ou manageur.</p>
            @error('manager_id')
                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>
        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="hidden" name="est_partenaire" value="0">
                <input type="checkbox" name="est_partenaire" value="1" {{ old('est_partenaire', $equipe->est_partenaire) ? 'checked' : '' }}
                       class="mt-1 rounded border-white/20 bg-white/5 text-amber-500 focus:ring-amber-500/50">
                <span class="text-sm">
                    <span class="font-medium text-white">Agence partenaire</span>
                    <span class="block text-[#94a3b8] mt-0.5">Si coché : les matchs de cette agence apparaissent dans <strong>Match Partenaire</strong>. Si décoché : dans <strong>Match Unions</strong> (Unions Agency).</span>
                </span>
            </label>
            @error('est_partenaire')
                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-medium">Enregistrer</button>
            <a href="{{ route('equipes.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white">Annuler</a>
        </div>
    </form>
</div>
@endsection
