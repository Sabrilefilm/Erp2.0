@extends('layouts.app')

@section('title', 'Le Message de l\'agence')

@push('styles')
<style>
/* Style blog : messages de l'agence, emojis et texte convivial */
.message-blog-card {
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.08);
    background: linear-gradient(165deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100%);
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.message-blog-card:hover {
    border-color: rgba(255,255,255,0.12);
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}
.message-blog-card .message-body {
    font-size: 1rem;
    line-height: 1.7;
    color: rgba(255,255,255,0.88);
    letter-spacing: 0.01em;
}
.message-blog-card .message-body br { display: block; margin-bottom: 0.6em; }
.message-blog-card .message-date {
    font-size: 0.8125rem;
    color: rgba(255,255,255,0.4);
}
.message-blog-card .message-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.3;
}
</style>
@endpush

@section('content')
<div class="space-y-6 pb-8 max-w-3xl mx-auto">
    {{-- Hero style blog --}}
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-500/25 via-violet-500/15 to-purple-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-3xl">💬</div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Le Message de l'agence</h1>
                    <p class="text-[#94a3b8] text-sm mt-1">Annonces, infos et messages pour tout le monde — emojis bienvenus ✨</p>
                </div>
            </div>
            @if(auth()->user()->canAddEntries())
            <a href="{{ route('regles.create') }}" class="ultra-btn-cta shrink-0"><span>+ Nouveau message</span></a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ {{ session('success') }}</div>
    @endif

    @if($regles->isEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center">
        <div class="w-20 h-20 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4 text-4xl">📭</div>
        <p class="text-[#94a3b8] text-lg">Aucun message pour le moment.</p>
        <p class="text-[#64748b] text-sm mt-1">L'agence pourra poster des annonces ici (avec des emojis si elle veut 😊).</p>
        @if(auth()->user()->canAddEntries())
        <a href="{{ route('regles.create') }}" class="inline-block mt-6 px-5 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white font-semibold text-sm">+ Nouveau message</a>
        @endif
    </div>
    @else
    <div class="space-y-5">
        @foreach($regles as $regle)
        <article class="message-blog-card p-5 md:p-6 {{ $regle->actif ? '' : 'opacity-70' }}">
            <header class="flex flex-wrap items-start justify-between gap-3 mb-4">
                <div class="min-w-0 flex-1">
                    <h2 class="message-title">{{ $regle->titre }}</h2>
                    @if($regle->updated_at)
                    <p class="message-date mt-1.5">📅 {{ $regle->updated_at->translatedFormat('d F Y \à H:i') }}</p>
                    @endif
                    @if(!$regle->actif)
                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-white/10 text-[#94a3b8] border border-white/10">Brouillon</span>
                    @endif
                    @if(auth()->user()->canAddEntries() && $regle->ordre !== null)
                    <span class="text-[11px] text-white/35 font-medium ml-2">Ordre {{ $regle->ordre }}</span>
                    @endif
                </div>
                @if(auth()->user()->canAddEntries())
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('regles.edit', $regle) }}" class="px-3 py-1.5 rounded-lg bg-indigo-500/20 text-indigo-400 hover:bg-indigo-500/30 text-sm font-medium">Modifier</a>
                    <form action="{{ route('regles.destroy', $regle) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce message ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 text-sm font-medium">Supprimer</button>
                    </form>
                </div>
                @endif
            </header>
            <div class="message-body">{!! nl2br(e($regle->contenu)) !!}</div>
        </article>
        @endforeach
    </div>
    @endif
</div>
@endsection
