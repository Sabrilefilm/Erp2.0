@extends('layouts.app')

@section('title', isset($isProfile) && $isProfile ? 'Mon profil' : 'Modifier ' . $user->name)

@section('content')
<div class="max-w-xl space-y-6">
    <h1 class="text-2xl font-bold bg-gradient-to-r from-neon-blue to-neon-purple bg-clip-text text-transparent">{{ isset($isProfile) && $isProfile ? 'Mon profil' : 'Modifier ' . $user->name }}</h1>

    <form action="{{ isset($isProfile) && $isProfile ? route('profile.update') : route('users.update', $user) }}" method="POST" class="ultra-card rounded-xl p-6 space-y-4 border border-white/10">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-[#b0bee3] mb-1">Nom</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="ultra-input w-full px-3 py-2 rounded-xl text-white">
            @error('name')<p class="mt-1 text-sm text-accent-red">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="username" class="block text-sm font-medium text-[#b0bee3] mb-1">Utilisateur (identifiant de connexion)</label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required class="ultra-input w-full px-3 py-2 rounded-xl text-white">
            @error('username')<p class="mt-1 text-sm text-accent-red">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-[#b0bee3] mb-1">Téléphone (optionnel)</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="ultra-input w-full px-3 py-2 rounded-xl text-white" placeholder="ex. 06 12 34 56 78">
            @error('phone')<p class="mt-1 text-sm text-accent-red">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="date_naissance" class="block text-sm font-medium text-[#b0bee3] mb-1">Date de naissance (pour anniversaire)</label>
            <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance', $user->date_naissance?->format('Y-m-d')) }}" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
            <p class="mt-1 text-xs text-[#94a3b8]">Permet d'afficher une animation le jour de ton anniversaire et de programmer des matchs « Anniversaire » pour toi.</p>
            @error('date_naissance')<p class="mt-1 text-sm text-accent-red">{{ $message }}</p>@enderror
        </div>
        @if(!isset($isProfile) || !auth()->user()->isAgent())
        <div>
            <label for="password" class="block text-sm font-medium text-[#b0bee3] mb-1">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
            <div class="relative flex items-stretch">
                <input type="password" name="password" id="password" class="ultra-input w-full px-3 py-2 pr-12 rounded-xl text-white">
                <button type="button" onclick="window.togglePassword('password', this)" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded text-[#b0bee3] hover:text-white focus:outline-none focus:ring-2 focus:ring-neon-blue/50" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
                    <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            @error('password')<p class="mt-1 text-sm text-accent-red">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-[#b0bee3] mb-1">Confirmer le mot de passe</label>
            <div class="relative flex items-stretch">
                <input type="password" name="password_confirmation" id="password_confirmation" class="ultra-input w-full px-3 py-2 pr-12 rounded-xl text-white">
                <button type="button" onclick="window.togglePassword('password_confirmation', this)" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded text-[#b0bee3] hover:text-white focus:outline-none focus:ring-2 focus:ring-neon-blue/50" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
                    <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
        </div>
        @endif
        @if(!isset($isProfile) && (auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur() || auth()->user()->isManageur() || (auth()->user()->isSousManager() && $user->equipe_id === auth()->user()->equipe_id)))
        @if(auth()->user()->isFondateurPrincipal() && isset($equipes) && $equipes->isNotEmpty())
        <div>
            <label for="equipe_id" class="block text-sm font-medium text-[#b0bee3] mb-1">Agence / Sous-agence</label>
            <select name="equipe_id" id="equipe_id" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                <option value="">— Aucune —</option>
                @foreach($equipes as $eq)
                <option value="{{ $eq->id }}" {{ old('equipe_id', $user->equipe_id) == $eq->id ? 'selected' : '' }}>{{ $eq->nom }}</option>
                @endforeach
            </select>
            <p class="text-xs text-[#94a3b8] mt-1">Réassigner à une autre agence ou sous-agence si besoin.</p>
        </div>
        @endif
        <div>
            <label for="role" class="block text-sm font-medium text-[#b0bee3] mb-1">Rôle</label>
            @php
                $roleLabels = [
                    'fondateur' => 'Fondateur',
                    'directeur' => 'Directeur',
                    'sous_directeur' => 'Sous-directeur',
                    'manageur' => 'Manageur',
                    'sous_manager' => 'Sous-manager',
                    'agent' => 'Agent',
                    'ambassadeur' => 'Ambassadeur',
                    'createur' => 'Créateur',
                ];
            @endphp
            <select name="role" id="role" required class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                @foreach($allowedRoles as $r)
                <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ $roleLabels[$r] ?? $r }}</option>
                @endforeach
            </select>
        </div>
        @if($user->role === 'createur')
        <div>
            <label for="agent_id" class="block text-sm font-medium text-[#b0bee3] mb-1">Agent</label>
            <select name="agent_id" id="agent_id" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                <option value="">— Aucun agent —</option>
                @foreach($agents as $agent)
                <option value="{{ $agent->id }}" {{ old('agent_id', $user->createur?->agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->name }}{{ $agent->equipe ? ' (' . $agent->equipe->nom . ')' : '' }}</option>
                @endforeach
            </select>
            <p class="text-xs text-[#94a3b8] mt-1">Uniquement pour les créateurs : l’agent qui les suit.</p>
        </div>
        @endif
        <div>
            <label for="manager_id" class="block text-sm font-medium text-[#b0bee3] mb-1">Manager</label>
            <select name="manager_id" id="manager_id" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                <option value="">—</option>
                @foreach($managers as $m)
                <option value="{{ $m->id }}" {{ old('manager_id', $user->manager_id) == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $roleLabels[$m->role] ?? $m->role }})</option>
                @endforeach
            </select>
            <p class="text-xs text-[#94a3b8] mt-1">Pour les agents et ambassadeurs : assigner un manageur.</p>
        </div>
        @endif
        @if(!isset($isProfile) && ($user->isLoginBlocked() || $user->must_change_password))
        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="clear_login_block" value="1" class="mt-1 rounded border-amber-500/50 text-amber-500 focus:ring-amber-500/50">
                <span class="text-sm text-amber-200">Débloquer la connexion (compte temporairement bloqué après tentatives de connexion). L'utilisateur devra définir un nouveau mot de passe à sa prochaine connexion.</span>
            </label>
        </div>
        @endif
        <div class="flex gap-3 pt-2">
            <button type="submit" class="ultra-btn-primary px-4 py-2.5 rounded-xl font-semibold text-sm"><span>Enregistrer</span></button>
            <a href="{{ isset($isProfile) && $isProfile ? route('dashboard') : route('users.index') }}" class="ultra-input px-4 py-2.5 rounded-xl text-[#b0bee3] hover:text-white transition-colors inline-block">Annuler</a>
        </div>
    </form>
</div>
@endsection
