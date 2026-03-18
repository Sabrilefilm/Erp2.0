@extends('layouts.app')

@section('title', 'Diagnostic')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <header class="border-b border-white/10 pb-4">
        <h1 class="text-xl font-bold text-white">Diagnostic</h1>
        <p class="text-sm text-[#94a3b8] mt-1">Vue d’ensemble : IP, connexion, serveur et base de données — tout en un.</p>
    </header>

    {{-- Réseau / Connexion --}}
    <section class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10 bg-white/5">
            <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9a9 9 0 009 9m0-9a9 9 0 019-9m-9 9a9 9 0 000 18z"/></svg>
                Réseau & connexion
            </h2>
        </div>
        <div class="p-4 space-y-3">
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">Adresse IP (client)</span>
                <span class="text-white font-mono">{{ $clientIp ?: '—' }}</span>
            </div>
            @if($forwardedFor || $realIp)
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">X-Forwarded-For</span>
                <span class="text-white font-mono text-xs">{{ $forwardedFor ?: '—' }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">X-Real-IP</span>
                <span class="text-white font-mono text-xs">{{ $realIp ?: '—' }}</span>
            </div>
            @endif
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">Méthode HTTP</span>
                <span class="text-white">{{ $method }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">URL actuelle</span>
                <span class="text-white font-mono text-xs break-all">{{ $url }}</span>
            </div>
            <div class="pt-2 border-t border-white/10">
                <span class="text-[#94a3b8] text-xs block mb-1">User-Agent</span>
                <span class="text-white/80 text-xs font-mono break-all">{{ $userAgent ?: '—' }}</span>
            </div>
        </div>
    </section>

    {{-- Connexion actuelle (utilisateur + session) --}}
    <section class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10 bg-white/5">
            <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Connexion actuelle
            </h2>
        </div>
        <div class="p-4 space-y-3">
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">Utilisateur</span>
                <span class="text-white">{{ $user->name }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">Email</span>
                <span class="text-white">{{ $user->email }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">Rôle</span>
                <span class="text-white">{{ $user->getRoleLabel() }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">Session (driver)</span>
                <span class="text-white font-mono">{{ $sessionDriver }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <span class="text-[#94a3b8] min-w-[120px]">ID de session</span>
                <span class="text-white/80 font-mono text-xs break-all">{{ $sessionId ?: '—' }}</span>
            </div>
        </div>
    </section>

    {{-- Serveur --}}
    <section class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10 bg-white/5">
            <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                Serveur & application
            </h2>
        </div>
        <div class="p-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div class="flex flex-wrap gap-x-2">
                    <span class="text-[#94a3b8]">PHP</span>
                    <span class="text-white font-mono">{{ $phpVersion }}</span>
                </div>
                <div class="flex flex-wrap gap-x-2">
                    <span class="text-[#94a3b8]">Laravel</span>
                    <span class="text-white font-mono">{{ $laravelVersion }}</span>
                </div>
                <div class="flex flex-wrap gap-x-2">
                    <span class="text-[#94a3b8]">Environnement</span>
                    <span class="text-white">{{ $env }}</span>
                </div>
                <div class="flex flex-wrap gap-x-2">
                    <span class="text-[#94a3b8]">Debug</span>
                    <span class="{{ $debug ? 'text-amber-400' : 'text-emerald-400' }}">{{ $debug ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex flex-wrap gap-x-2">
                    <span class="text-[#94a3b8]">Timezone</span>
                    <span class="text-white">{{ $timezone }}</span>
                </div>
                <div class="flex flex-wrap gap-x-2">
                    <span class="text-[#94a3b8]">Locale</span>
                    <span class="text-white">{{ $locale }}</span>
                </div>
            </div>
            <div class="pt-2 border-t border-white/10 flex flex-wrap items-center gap-2 text-sm">
                <span class="text-[#94a3b8]">Base de données</span>
                <span class="font-mono text-white">{{ $dbDriver }}</span>
                @if($dbName)
                <span class="text-white/80">({{ $dbName }})</span>
                @endif
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium {{ $dbStatus === 'OK' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                    {{ $dbStatus }}
                </span>
                @if($dbError)
                <span class="text-red-400 text-xs block w-full mt-1">{{ $dbError }}</span>
                @endif
            </div>
            @if($appUrl)
            <div class="pt-2 border-t border-white/10 flex flex-wrap gap-x-2 text-sm">
                <span class="text-[#94a3b8]">URL app (config)</span>
                <span class="text-white font-mono text-xs break-all">{{ $appUrl }}</span>
            </div>
            @endif
        </div>
    </section>

    {{-- Maintenance : seul le Fondateur principal peut activer/désactiver --}}
    <section class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10 bg-white/5">
            <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Service en maintenance
            </h2>
        </div>
        <div class="p-4 space-y-3">
            @if(session('success'))
                <p class="text-sm text-emerald-400">{{ session('success') }}</p>
            @endif
            <p class="text-sm text-[#94a3b8]">En mode maintenance, seul le Fondateur principal peut se connecter. Les autres utilisateurs voient une page « Service en maintenance ».</p>
            @if($maintenanceEnabled)
                <form action="{{ route('diagnostic.maintenance.deactivate') }}" method="POST" class="inline" onsubmit="return confirm('Désactiver la maintenance ? Le site sera à nouveau accessible à tous.');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-amber-500/20 text-amber-400 border border-amber-500/40 hover:bg-amber-500/30 transition">
                        Désactiver la maintenance
                    </button>
                </form>
            @else
                <form action="{{ route('diagnostic.maintenance.activate') }}" method="POST" class="inline" onsubmit="return confirm('Mettre le site en maintenance ? Seul le Fondateur principal pourra se connecter.');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-amber-500/20 text-amber-400 border border-amber-500/40 hover:bg-amber-500/30 transition">
                        Mettre en maintenance
                    </button>
                </form>
            @endif
        </div>
    </section>

    <p class="text-xs text-[#64748b] text-center">Page générée à {{ now()->translatedFormat('d/m/Y H:i') }}</p>
</div>
@endsection
