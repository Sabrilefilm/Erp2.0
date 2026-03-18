@extends('layouts.app')

@section('title', 'Historique des notifications push')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <header class="border-b border-white/10 pb-4">
        <a href="{{ route('push-admin.index') }}" class="text-sm text-[#94a3b8] hover:text-white">← Notifications Push</a>
        <h1 class="text-xl font-bold text-white mt-2">Historique</h1>
        <p class="text-sm text-[#94a3b8] mt-1">Notifications envoyées avec date, cible et nombre de destinataires.</p>
    </header>

    <div class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        <ul class="divide-y divide-white/10">
            @forelse($logs as $log)
            <li class="px-4 py-3">
                <p class="text-sm text-white font-medium">{{ $log->title }}</p>
                @if($log->body)
                <p class="text-xs text-[#94a3b8] mt-0.5">{{ Str::limit($log->body, 120) }}</p>
                @endif
                <p class="text-xs text-[#64748b] mt-1">
                    {{ $log->sent_at->format('d/m/Y H:i') }}
                    — {{ $log->recipients_count }} destinataire(s)
                    @if($log->opened_count > 0)
                    — {{ $log->opened_count }} ouverture(s)
                    @endif
                    — {{ $log->target_type }}{{ $log->target_value ? ' : ' . $log->target_value : '' }}
                    @if($log->sender)
                    — par {{ $log->sender->name }}
                    @endif
                </p>
            </li>
            @empty
            <li class="px-4 py-8 text-center text-[#94a3b8] text-sm">Aucun envoi pour l'instant.</li>
            @endforelse
        </ul>
        <div class="px-4 py-3 border-t border-white/10">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
