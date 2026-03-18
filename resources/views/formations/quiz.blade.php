@extends('layouts.app')

@section('title', 'Quiz · ' . $formation->titre)

@section('content')
<div class="space-y-6 pb-8 max-w-2xl mx-auto">
    <div class="flex items-center gap-4">
        <a href="{{ route('formations.show', $formation) }}" class="p-2 -ml-2 rounded-xl text-[#94a3b8] hover:text-white hover:bg-white/10 transition-colors shrink-0" title="Retour">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="min-w-0 flex-1 text-center">
            <p class="text-xs text-[#94a3b8] uppercase tracking-wider">Quiz</p>
            <h1 class="text-lg font-bold text-white truncate">{{ $formation->titre }}</h1>
        </div>
        <span class="w-10 shrink-0"></span>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <p class="text-xs font-semibold text-neon-blue uppercase tracking-wider">{{ $questions->count() }} questions</p>
        <h2 class="text-xl font-bold text-white mt-1">Vérifiez vos connaissances</h2>
        <p class="text-sm text-[#94a3b8] mt-1">Répondez à toutes les questions puis validez.</p>
        <div class="flex flex-wrap items-center gap-2 mt-4">
            <span class="text-xs text-[#94a3b8]">Niveau :</span>
            @foreach(\App\Models\FormationQuestion::DIFFICULTES as $val => $label)
            <a href="{{ route('formations.quiz.show', ['formation' => $formation, 'difficulte' => $val]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $difficulte === $val ? 'bg-neon-blue/20 text-neon-blue border border-neon-blue/40' : 'bg-white/5 text-[#94a3b8] hover:bg-white/10 hover:text-white border border-white/10' }}">{{ $label }}</a>
            @endforeach
            <a href="{{ route('formations.quiz.show', ['formation' => $formation, 'difficulte' => 'toutes']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $difficulte === 'toutes' ? 'bg-neon-blue/20 text-neon-blue border border-neon-blue/40' : 'bg-white/5 text-[#94a3b8] hover:bg-white/10 hover:text-white border border-white/10' }}">Toutes</a>
        </div>
    </div>

    <form action="{{ route('formations.quiz.submit', $formation) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="difficulte" value="{{ $difficulte }}">
        @foreach($questions as $index => $question)
        <div class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden p-5 md:p-6">
            <p class="text-xs font-semibold text-neon-blue uppercase tracking-wider mb-2">Question {{ $index + 1 }}</p>
            <p class="text-white font-medium mb-4">{{ $question->question }}</p>

            @if($question->type === \App\Models\FormationQuestion::TYPE_VRAI_FAUX)
            <div class="flex flex-wrap gap-3">
                @foreach($question->reponses as $reponse)
                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl bg-white/5 border border-white/10 hover:border-neon-blue/40 transition-colors has-[:checked]:border-neon-blue has-[:checked]:bg-neon-blue/10">
                    <input type="radio" name="reponses[{{ $question->id }}]" value="{{ $reponse->id }}" required class="text-neon-blue focus:ring-neon-blue">
                    <span class="text-white">{{ $reponse->texte }}</span>
                </label>
                @endforeach
            </div>
            @elseif($question->type === \App\Models\FormationQuestion::TYPE_QUESTION_SIMPLE)
            <input type="text" name="reponses[{{ $question->id }}]" required placeholder="Votre réponse" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-[#64748b] focus:outline-none focus:border-neon-blue/50">
            @else
            <ul class="space-y-2">
                @foreach($question->reponses as $reponse)
                <li>
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl bg-white/5 border border-white/10 hover:border-neon-blue/40 transition-colors has-[:checked]:border-neon-blue has-[:checked]:bg-neon-blue/10">
                        <input type="radio" name="reponses[{{ $question->id }}]" value="{{ $reponse->id }}" required class="text-neon-blue focus:ring-neon-blue">
                        <span class="text-white">{{ $reponse->texte }}</span>
                    </label>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit" class="flex-1 py-4 rounded-2xl bg-neon-blue hover:bg-neon-blue/90 text-[#0f172a] font-bold text-center transition-colors shadow-lg shadow-neon-blue/20">
                Valider le quiz
            </button>
            <a href="{{ route('formations.show', $formation) }}" class="py-4 px-6 rounded-2xl border border-white/20 text-[#94a3b8] hover:text-white hover:border-white/40 text-center font-medium transition-colors">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
