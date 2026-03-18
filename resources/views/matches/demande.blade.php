@extends('layouts.app')

@section('title', 'Demander un match')

@section('content')
<div class="space-y-6 max-w-xl">
    <h1 class="text-2xl font-bold bg-gradient-to-r from-neon-blue to-neon-purple bg-clip-text text-transparent">Demander un match à mon agent</h1>
    <p class="text-[#b0bee3] text-sm">Votre agent pourra valider et programmer le match après réception de votre demande.</p>

    @if(session('error'))
    <div class="ultra-card rounded-lg px-4 py-3 border border-red-500/30 bg-red-500/10 text-red-400 text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="ultra-card rounded-xl p-5 border border-white/10">
        <form action="{{ route('matches.demande.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Date souhaitée *</label>
                    <input type="date" name="date_souhaitee" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('date_souhaitee', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                    @error('date_souhaitee')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Heure souhaitée</label>
                    <input type="time" name="heure_souhaitee" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('heure_souhaitee') }}">
                    @error('heure_souhaitee')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Type de match *</label>
                <select name="type" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                    @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">@ de la personne en face (optionnel)</label>
                <input type="text" name="qui_en_face" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('qui_en_face') }}" placeholder="Ex. @username" maxlength="100">
                @error('qui_en_face')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Message pour votre agent (optionnel)</label>
                <textarea name="message" rows="3" placeholder="Précisions, contraintes..." class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm resize-none" maxlength="500">{{ old('message') }}</textarea>
                @error('message')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-lg text-sm font-semibold"><span>Envoyer la demande</span></button>
                <a href="{{ route('matches.index') }}" class="ultra-input px-4 py-2 rounded-lg text-sm font-medium text-[#b0bee3] hover:text-white transition-colors inline-block">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
