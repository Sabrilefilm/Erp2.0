@extends('layouts.app')

@section('title', 'Générer un code de réinitialisation')

@push('styles')
<style>
    @keyframes pwd-hero-in {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pwd-card-in {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pwd-code-glow {
        0%, 100% { box-shadow: 0 0 24px rgba(16, 185, 129, 0.25); }
        50% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.4); }
    }
    @keyframes pwd-code-reveal {
        from { opacity: 0; transform: scale(0.92); letter-spacing: 0.4em; }
        to { opacity: 1; transform: scale(1); letter-spacing: 0.2em; }
    }
    @keyframes pwd-success-tick {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes pwd-timer-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
    @keyframes pwd-timer-warn {
        0%, 100% { color: rgb(252 211 77); }
        50% { color: rgb(245 158 11); }
    }
    .pwd-hero { animation: pwd-hero-in 0.5s ease-out forwards; }
    .pwd-card { animation: pwd-card-in 0.45s ease-out forwards; }
    .pwd-card-delay-1 { animation-delay: 0.06s; animation-fill-mode: both; }
    .pwd-card-delay-2 { animation-delay: 0.12s; animation-fill-mode: both; }
    .pwd-code-box { animation: pwd-code-glow 2.5s ease-in-out infinite; }
    .pwd-code-text { animation: pwd-code-reveal 0.5s ease-out 0.15s forwards; opacity: 0; }
    .pwd-copy-btn { transition: background 0.2s, transform 0.15s; }
    .pwd-copy-btn:hover { transform: translateY(-1px); }
    .pwd-copy-btn.copied .pwd-copy-icon-default { display: none; }
    .pwd-copy-btn.copied .pwd-copy-icon-done { display: inline-flex; animation: pwd-success-tick 0.35s ease-out; }
    .pwd-copy-btn .pwd-copy-icon-done { display: none; }
    .pwd-timer.warn { animation: pwd-timer-warn 1.5s ease-in-out infinite; }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-8 max-w-xl mx-auto">
    {{-- Hero --}}
    <div class="pwd-hero rounded-2xl overflow-hidden bg-gradient-to-br from-amber-600/25 via-amber-700/20 to-orange-800/20 border border-amber-500/25 p-6 shadow-lg">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center shrink-0">
                <span class="text-3xl" aria-hidden="true">🔐</span>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl font-bold text-white">Générer un code de réinitialisation</h1>
                <p class="text-amber-200/90 text-sm mt-2 leading-relaxed">
                    Choisissez un utilisateur pour générer un code à usage unique. Transmettez le code à la personne concernée ; elle pourra réinitialiser son mot de passe sur la page de connexion avec son <strong class="text-amber-100">identifiant</strong> et ce code.
                </p>
            </div>
        </div>
    </div>

    @if (session('generated_code'))
        {{-- Bloc code au centre : avec chronomètre --}}
        <div class="pwd-card pwd-card-delay-1 rounded-2xl border-2 border-emerald-500/40 bg-gradient-to-br from-emerald-950/50 to-emerald-900/40 p-6 md:p-8 shadow-xl text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/20 border border-emerald-400/40 px-3 py-1 mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-semibold text-emerald-300">Code généré</span>
            </div>
            <p class="text-sm text-white/80 mb-4">Pour <strong class="text-white">{{ session('generated_for_user') }}</strong> ({{ session('generated_for_username') }})</p>

            <div class="pwd-code-box mt-4 mb-5 rounded-2xl bg-black/40 border border-emerald-500/40 py-6 px-6">
                <p class="pwd-code-text text-2xl md:text-4xl font-mono font-bold text-emerald-300 tracking-[0.2em] select-all" id="generated-code">{{ session('generated_code') }}</p>
            </div>

            {{-- Chronomètre (countdown) --}}
            <div class="flex flex-col items-center gap-1 mb-5">
                <span class="text-xs font-medium text-[#94a3b8] uppercase tracking-wider">Temps restant</span>
                <div id="pwd-countdown" class="pwd-timer text-2xl md:text-3xl font-mono font-bold text-amber-300 tabular-nums" data-expires-at="{{ session('code_expires_at')->getTimestamp() }}">
                    --:--
                </div>
                <p id="pwd-countdown-done" class="text-sm font-medium text-rose-400 hidden">Code expiré</p>
            </div>

            <p class="text-xs text-[#94a3b8] mb-4">Valide jusqu'à <strong class="text-white/80">{{ session('code_expires_at')->format('d/m/Y à H:i') }}</strong>. Transmettez ce code en toute sécurité.</p>
            <button type="button" id="pwd-copy-btn" class="pwd-copy-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-sm font-medium hover:bg-emerald-500/30">
                <span class="pwd-copy-icon-default inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Copier le code
                </span>
                <span class="pwd-copy-icon-done inline-flex items-center gap-2 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Copié !
                </span>
            </button>
        </div>
    @endif

    {{-- Formulaire : au centre / en bas — pour générer ou regénérer --}}
    <div class="pwd-card pwd-card-delay-2 ultra-card rounded-2xl p-6 border border-white/10 shadow-lg">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">1</span>
            Choisir l'utilisateur
        </h2>
        <form action="{{ route('password.generate-code') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="user_id" class="block text-sm font-medium text-[#b0bee3] mb-1.5">Utilisateur</label>
                <select name="user_id" id="user_id" required class="ultra-input w-full px-3 py-2.5 rounded-xl text-white border border-white/10 focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30 transition-colors">
                    <option value="">— Choisir un utilisateur —</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->username }}) — {{ $u->getRoleLabel() }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold shadow-lg shadow-amber-500/25 transition-all duration-200 hover:shadow-amber-500/35 hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Générer le code
            </button>
        </form>
    </div>
</div>

@if (session('generated_code'))
<script>
(function () {
    var copyBtn = document.getElementById('pwd-copy-btn');
    var codeEl = document.getElementById('generated-code');
    if (copyBtn && codeEl) {
        copyBtn.addEventListener('click', function () {
            var code = (codeEl.textContent || '').trim();
            if (!code) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(function () {
                    copyBtn.classList.add('copied');
                    setTimeout(function () { copyBtn.classList.remove('copied'); }, 2000);
                });
            } else {
                var input = document.createElement('input');
                input.value = code;
                input.style.position = 'fixed';
                input.style.opacity = '0';
                document.body.appendChild(input);
                input.select();
                try {
                    document.execCommand('copy');
                    copyBtn.classList.add('copied');
                    setTimeout(function () { copyBtn.classList.remove('copied'); }, 2000);
                } catch (e) {}
                document.body.removeChild(input);
            }
        });
    }

    var countdownEl = document.getElementById('pwd-countdown');
    var countdownDoneEl = document.getElementById('pwd-countdown-done');
    if (countdownEl && countdownEl.dataset.expiresAt) {
        var expiresAt = parseInt(countdownEl.dataset.expiresAt, 10) * 1000;

        function updateCountdown() {
            var now = Date.now();
            var left = Math.max(0, Math.floor((expiresAt - now) / 1000));
            if (left <= 0) {
                countdownEl.classList.add('hidden');
                if (countdownDoneEl) countdownDoneEl.classList.remove('hidden');
                return;
            }
            var m = Math.floor(left / 60);
            var s = left % 60;
            countdownEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
            if (left <= 300) countdownEl.classList.add('warn');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
})();
</script>
@endif
@endsection
