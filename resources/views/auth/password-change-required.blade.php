@extends('layouts.app')

@section('title', 'Changement de mot de passe obligatoire')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 md:p-8 space-y-6">
        <div class="text-center">
            <div class="w-14 h-14 mx-auto rounded-xl bg-amber-500/20 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h1 class="text-xl font-bold text-white">Changement de mot de passe obligatoire</h1>
            <p class="text-sm text-[#94a3b8] mt-2">Votre compte a été débloqué. Veuillez définir un nouveau mot de passe pour continuer.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl bg-red-500/20 border border-red-500/40 text-red-400 text-sm px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.change-required.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="password" class="block text-sm font-medium text-white/90 mb-1">Nouveau mot de passe</label>
                <input type="password" name="password" id="password" required autofocus
                       class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/40 focus:ring-2 focus:ring-[#00d4ff]/50 focus:border-[#00d4ff]/50"
                       placeholder="Nouveau mot de passe" autocomplete="new-password">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-white/90 mb-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/40 focus:ring-2 focus:ring-[#00d4ff]/50 focus:border-[#00d4ff]/50"
                       placeholder="Confirmer le mot de passe" autocomplete="new-password">
            </div>
            <button type="submit" class="w-full py-3 rounded-xl font-semibold text-white bg-[#00d4ff]/20 border border-[#00d4ff]/40 hover:bg-[#00d4ff]/30 transition">
                Enregistrer le mot de passe
            </button>
        </form>
    </div>
</div>
@endsection
