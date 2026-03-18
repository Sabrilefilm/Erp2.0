@extends('layouts.app')

@section('title', 'Résultat quiz · ' . $formation->titre)

@section('content')
<div class="space-y-6 pb-8 max-w-xl mx-auto">
    <a href="{{ route('formations.show', $formation) }}" class="inline-flex items-center gap-2 text-sm text-[#94a3b8] hover:text-white transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour au module
    </a>

    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 md:p-8 text-center">
        <span class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 {{ $total > 0 && ($score / $total) >= 0.6 ? 'bg-neon-green/20' : 'bg-white/10' }}">
            @if($total > 0 && ($score / $total) >= 0.6)
            <svg class="w-8 h-8 text-neon-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
            <svg class="w-8 h-8 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </span>
        <h1 class="text-2xl font-bold text-white">Quiz terminé</h1>
        <p class="text-sm text-[#94a3b8] mt-1">{{ $formation->titre }}</p>
        @if($score !== null && $total !== null)
        <p class="mt-6 text-3xl font-bold text-white">{{ $score }} / {{ $total }}</p>
        <p class="text-sm text-[#94a3b8] mt-1">{{ $total > 0 ? round(($score / $total) * 100, 0) : 0 }} %</p>
        @if($total > 0 && ($score / $total) >= 0.6)
        <p class="mt-4 text-sm font-semibold text-neon-green">Bravo, vous avez validé ce module.</p>
        @else
        <p class="mt-4 text-sm text-[#94a3b8]">Vous pouvez repasser le quiz après avoir revu le contenu.</p>
        @endif
        @endif
    </div>

    @if($dernieres_tentatives->isNotEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
        <h2 class="text-sm font-semibold text-white mb-3">Vos dernières tentatives</h2>
        <ul class="space-y-2 text-sm">
            @foreach($dernieres_tentatives as $t)
            <li class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                <span class="text-[#94a3b8]">{{ $t->completed_at?->format('d/m/Y H:i') }}</span>
                <span class="font-medium text-white">{{ $t->score }} / {{ $t->total }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('formations.quiz.show', ['formation' => $formation, 'difficulte' => request()->get('difficulte', 'moyen')]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-neon-blue hover:bg-neon-blue/90 text-[#0f172a] font-semibold text-sm transition-colors shadow-lg shadow-neon-blue/20">
            Repasser le quiz
        </a>
        <a href="{{ route('formations.contenu', $formation) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-white/20 text-[#94a3b8] hover:text-white hover:border-white/40 text-sm font-medium transition-colors">
            Revoir le cours
        </a>
        <a href="{{ route('formations.show', $formation) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-white/20 text-[#94a3b8] hover:text-white hover:border-white/40 text-sm font-medium transition-colors">
            Retour au module
        </a>
    </div>
</div>
@endsection
