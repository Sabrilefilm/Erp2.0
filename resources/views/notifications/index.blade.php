@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="notifications-page pb-24 md:pb-8">
    {{-- Header --}}
    <div class="mb-4">
        <h1 class="text-2xl md:text-3xl font-bold text-white">Notifications</h1>
    </div>

    {{-- Onglets Tout | Non lu --}}
    <div class="flex gap-1 p-1 rounded-xl bg-white/5 border border-white/10 w-fit mb-6">
        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ ($filter ?? 'all') === 'all' ? 'bg-[#1877f2] text-white' : 'text-[#94a3b8] hover:text-white' }}">
            Tout
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ ($filter ?? '') === 'unread' ? 'bg-[#1877f2] text-white' : 'text-[#94a3b8] hover:text-white' }}">
            Non lu
        </a>
    </div>

    @php
        $iconByType = [
            'match_programme' => ['bg' => 'bg-emerald-500/20', 'ring' => 'bg-emerald-500', 'icon' => 'calendar'],
            'demande_match' => ['bg' => 'bg-amber-500/20', 'ring' => 'bg-amber-500', 'icon' => 'bell'],
            'rappel_match' => ['bg' => 'bg-cyan-500/20', 'ring' => 'bg-cyan-500', 'icon' => 'clock'],
            'recompense_attribuee' => ['bg' => 'bg-neon-green/20', 'ring' => 'bg-neon-green', 'icon' => 'gift'],
        ];
    @endphp

    {{-- Section Nouveau --}}
    @if($nouveau->isNotEmpty())
    <section class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-[#94a3b8] uppercase tracking-wider">Nouveau</h2>
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="text-sm text-[#1877f2] hover:underline">Voir tout</a>
        </div>
        <div class="space-y-0 divide-y divide-white/5 rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
            @foreach($nouveau as $notif)
            @php
                $data = is_array($notif->data) ? $notif->data : (array) $notif->data;
                $type = $data['type'] ?? 'match_programme';
                $style = $iconByType[$type] ?? $iconByType['match_programme'];
            @endphp
            <a href="{{ route('notifications.read', $notif->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/5 transition-colors {{ $notif->read_at ? 'opacity-80' : '' }}">
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-full {{ $style['bg'] }} flex items-center justify-center text-white">
                        @if(($style['icon'] ?? '') === 'calendar')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif(($style['icon'] ?? '') === 'bell')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @elseif(($style['icon'] ?? '') === 'gift')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-5 h-5 rounded-full {{ $style['ring'] }} border-2 border-[#0f172a] flex items-center justify-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    </span>
                </div>
                <div class="min-w-0 flex-1 pt-0.5 overflow-hidden">
                    <p class="text-sm text-white leading-snug break-words" style="overflow-wrap: break-word; word-break: break-word;">{{ $data['message'] ?? 'Notification' }}</p>
                    <p class="text-xs text-[#64748b] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notif->read_at)
                <span class="w-2.5 h-2.5 rounded-full bg-[#1877f2] flex-shrink-0 mt-4" aria-hidden="true"></span>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Section Aujourd'hui --}}
    @if($aujourdhui->isNotEmpty())
    <section class="mb-8">
        <h2 class="text-sm font-semibold text-[#94a3b8] uppercase tracking-wider mb-3">Aujourd'hui</h2>
        <div class="space-y-0 divide-y divide-white/5 rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
            @foreach($aujourdhui as $notif)
            @php
                $data = is_array($notif->data) ? $notif->data : (array) $notif->data;
                $type = $data['type'] ?? 'match_programme';
                $style = $iconByType[$type] ?? $iconByType['match_programme'];
            @endphp
            <a href="{{ route('notifications.read', $notif->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/5 transition-colors {{ $notif->read_at ? 'opacity-80' : '' }}">
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-full {{ $style['bg'] }} flex items-center justify-center text-white">
                        @if(($style['icon'] ?? '') === 'calendar')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif(($style['icon'] ?? '') === 'bell')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @elseif(($style['icon'] ?? '') === 'gift')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-5 h-5 rounded-full {{ $style['ring'] }} border-2 border-[#0f172a] flex items-center justify-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    </span>
                </div>
                <div class="min-w-0 flex-1 pt-0.5 overflow-hidden">
                    <p class="text-sm text-white leading-snug break-words" style="overflow-wrap: break-word; word-break: break-word;">{{ $data['message'] ?? 'Notification' }}</p>
                    <p class="text-xs text-[#64748b] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notif->read_at)
                <span class="w-2.5 h-2.5 rounded-full bg-[#1877f2] flex-shrink-0 mt-4" aria-hidden="true"></span>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Section Plus tôt --}}
    @if($plusTot->isNotEmpty())
    <section class="mb-8">
        <h2 class="text-sm font-semibold text-[#94a3b8] uppercase tracking-wider mb-3">Plus tôt</h2>
        <div class="space-y-0 divide-y divide-white/5 rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
            @foreach($plusTot as $notif)
            @php
                $data = is_array($notif->data) ? $notif->data : (array) $notif->data;
                $type = $data['type'] ?? 'match_programme';
                $style = $iconByType[$type] ?? $iconByType['match_programme'];
            @endphp
            <a href="{{ route('notifications.read', $notif->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/5 transition-colors {{ $notif->read_at ? 'opacity-80' : '' }}">
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-full {{ $style['bg'] }} flex items-center justify-center text-white">
                        @if(($style['icon'] ?? '') === 'calendar')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif(($style['icon'] ?? '') === 'bell')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @elseif(($style['icon'] ?? '') === 'gift')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-5 h-5 rounded-full {{ $style['ring'] }} border-2 border-[#0f172a] flex items-center justify-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    </span>
                </div>
                <div class="min-w-0 flex-1 pt-0.5 overflow-hidden">
                    <p class="text-sm text-white leading-snug break-words" style="overflow-wrap: break-word; word-break: break-word;">{{ $data['message'] ?? 'Notification' }}</p>
                    <p class="text-xs text-[#64748b] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notif->read_at)
                <span class="w-2.5 h-2.5 rounded-full bg-[#1877f2] flex-shrink-0 mt-4" aria-hidden="true"></span>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    @if($notifications->isEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/[0.02] px-6 py-16 text-center">
        <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-4 text-4xl">🔔</div>
        <p class="text-[#94a3b8]">Aucune notification pour le moment.</p>
    </div>
    @else
    {{-- Bouton voir les notifications précédentes --}}
    <div class="mt-6 text-center">
        <a href="{{ route('notifications.index', ['filter' => $filter ?? 'all']) }}" class="inline-block px-6 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white text-sm font-medium transition-colors border border-white/10">
            Voir les notifications précédentes
        </a>
    </div>
    @endif
</div>
@endsection
