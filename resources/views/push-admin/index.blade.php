@extends('layouts.app')
@section('title', 'Notifications Push')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <header class="border-b border-white/10 pb-4">
        <h1 class="text-xl font-bold text-white">Notifications Push</h1>
        <p class="text-sm text-[#94a3b8] mt-1">Envoyer des notifications sur les telephones et ordinateurs des utilisateurs.</p>
    </header>
    @if(!$configured)
    <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-amber-200 text-sm">
        Configuration requise : ajoutez WEBPUSH_VAPID_PUBLIC_KEY, WEBPUSH_VAPID_PRIVATE_KEY et WEBPUSH_VAPID_SUBJECT dans .env
    </div>
    @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('push-admin.send') }}" class="rounded-xl border border-white/10 bg-white/5 p-5 hover:bg-white/10 transition-colors block">
            <h2 class="text-base font-semibold text-white">Envoyer une notification</h2>
            <p class="text-sm text-[#94a3b8] mt-1">A un utilisateur, un role ou a tous.</p>
        </a>
        <a href="{{ route('push-admin.templates') }}" class="rounded-xl border border-white/10 bg-white/5 p-5 hover:bg-white/10 transition-colors block">
            <h2 class="text-base font-semibold text-white">Messages predefinis</h2>
            <p class="text-sm text-[#94a3b8] mt-1">Creer et gerer les modeles.</p>
        </a>
        <a href="{{ route('push-admin.scheduled') }}" class="rounded-xl border border-white/10 bg-white/5 p-5 hover:bg-white/10 transition-colors block">
            <h2 class="text-base font-semibold text-white">Planifier un envoi</h2>
            <p class="text-sm text-[#94a3b8] mt-1">Programmer une notification.</p>
            @if($scheduledCount > 0)<p class="text-xs text-cyan-400 mt-2">{{ $scheduledCount }} en attente</p>@endif
        </a>
        <a href="{{ route('push-admin.history') }}" class="rounded-xl border border-white/10 bg-white/5 p-5 hover:bg-white/10 transition-colors block">
            <h2 class="text-base font-semibold text-white">Historique</h2>
            <p class="text-sm text-[#94a3b8] mt-1">Voir les notifications envoyees.</p>
        </a>
    </div>
    @if($recentLogs->isNotEmpty())
    <section class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-white">Derniers envois</h2>
            <a href="{{ route('push-admin.history') }}" class="text-xs text-[#1877f2] hover:underline">Voir tout</a>
        </div>
        <ul class="divide-y divide-white/10">
            @foreach($recentLogs as $log)
            <li class="px-4 py-3">
                <p class="text-sm text-white font-medium">{{ $log->title }}</p>
                <p class="text-xs text-[#94a3b8] mt-0.5">{{ $log->sent_at->format('d/m/Y H:i') }} - {{ $log->recipients_count }} destinataire(s)</p>
            </li>
            @endforeach
        </ul>
    </section>
    @endif
</div>
@endsection
