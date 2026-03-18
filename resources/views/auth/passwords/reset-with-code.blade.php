<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Réinitialiser avec un code — Ultra</title>
    @include('partials.head-assets')
    <link rel="stylesheet" href="{{ asset('css/login-modern.css') }}">
</head>
<body class="min-h-screen bg-[#0a0e27]">
    <div class="lg:hidden login-wrap">
        <div class="login-bg" aria-hidden="true"></div>
        <div class="login-orb login-orb-1" aria-hidden="true"></div>
        <div class="login-orb login-orb-2" aria-hidden="true"></div>
        <div class="login-orb login-orb-3" aria-hidden="true"></div>
        <span class="login-hero-text" aria-hidden="true">Bienvenue</span>
        <div class="login-card">
            <p class="login-app-name">Ultra</p>
            <h1 class="login-title">Réinitialiser le mot de passe</h1>
            <p class="text-sm text-white/60 mb-4">Entrez votre identifiant, le code reçu et votre nouveau mot de passe.</p>

            @if ($errors->any())
                <div class="mb-4 space-y-1">
                    @foreach ($errors->all() as $err)
                        <p class="text-sm text-red-400">{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset-with-code') }}">
                @csrf
                <div class="login-input-wrap">
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                           class="login-input" placeholder="Identifiant (utilisateur)" autocomplete="username">
                </div>
                <div class="login-input-wrap">
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           class="login-input font-mono tracking-widest" placeholder="Code à 8 caractères" maxlength="8" autocomplete="one-time-code">
                </div>
                <div class="login-input-wrap">
                    <input type="password" name="password" id="password" required
                           class="login-input" placeholder="Nouveau mot de passe" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password', this)" class="login-eye" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
                        <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-closed hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <div class="login-input-wrap">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="login-input" placeholder="Confirmer le mot de passe" autocomplete="new-password">
                </div>
                <button type="submit" class="login-btn">Réinitialiser le mot de passe</button>
            </form>

            <p class="login-footer mt-4">
                <a href="{{ route('login') }}" class="text-[#00d4ff] hover:underline">Retour à la connexion</a>
            </p>
            <div class="login-legal mt-4">
                <a href="{{ route('legal.contrat') }}">Contrat</a>
                <span>·</span>
                <a href="{{ route('legal.reglement') }}">Règlement</a>
                <span>·</span>
                <a href="{{ route('legal.rgpd') }}">RGPD</a>
                <span>·</span>
                <a href="{{ route('legal.confidentialite') }}">Politique de Confidentialité</a>
                <span>·</span>
                <a href="{{ route('legal.mentions') }}">Mentions Légales</a>
            </div>
        </div>
    </div>

    <div class="hidden lg:flex min-h-screen w-full">
        <div class="hidden lg:flex lg:flex-1 lg:min-w-0 lg:max-w-[48%] login-desk-left">
            <div class="login-desk-orb login-desk-orb-1" aria-hidden="true"></div>
            <div class="login-desk-orb login-desk-orb-2" aria-hidden="true"></div>
            <div class="login-desk-content flex flex-col justify-between p-12 xl:p-20 w-full">
                <div>
                    <div class="login-desk-logo">U</div>
                    <p class="login-desk-tag">Ultra</p>
                    <h1 class="login-desk-title">Réinitialiser avec un code</h1>
                    <p class="login-desk-desc">
                        Utilisez l’identifiant de votre compte et le code fourni par l’administrateur pour définir un nouveau mot de passe.
                    </p>
                </div>
                <div>
                    <p class="login-desk-copy">© {{ date('Y') }} Ultra.</p>
                    <div class="login-legal mt-2 justify-start">
                        <a href="{{ route('legal.contrat') }}">Contrat</a>
                        <span>·</span>
                        <a href="{{ route('legal.reglement') }}">Règlement</a>
                        <span>·</span>
                        <a href="{{ route('legal.rgpd') }}">RGPD</a>
                        <span>·</span>
                        <a href="{{ route('legal.confidentialite') }}">Politique de Confidentialité</a>
                        <span>·</span>
                        <a href="{{ route('legal.mentions') }}">Mentions Légales</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden lg:flex flex-1 login-desk-right">
            <span class="login-hero-text" aria-hidden="true">Bienvenue</span>
            <div class="w-full max-w-md px-6">
                <div class="login-desk-form-wrap">
                    <p class="login-app-name">Ultra</p>
                    <h2 class="login-title">Réinitialiser le mot de passe</h2>
                    <p class="text-sm text-white/50 text-center -mt-2 mb-6">Identifiant + code reçu + nouveau mot de passe.</p>

                    @if ($errors->any())
                        <div class="mb-4 space-y-1">
                            @foreach ($errors->all() as $err)
                                <p class="text-sm text-red-400">{{ $err }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.reset-with-code') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="username-pc" class="login-desk-label">Identifiant (utilisateur)</label>
                            <input type="text" name="username" id="username-pc" value="{{ old('username') }}" required autofocus
                                   class="login-desk-input w-full" placeholder="Votre identifiant" autocomplete="username">
                        </div>
                        <div>
                            <label for="code-pc" class="login-desk-label">Code reçu</label>
                            <input type="text" name="code" id="code-pc" value="{{ old('code') }}" required
                                   class="login-desk-input w-full font-mono tracking-widest" placeholder="Code à 8 caractères" maxlength="8" autocomplete="one-time-code">
                        </div>
                        <div>
                            <label for="password-pc" class="login-desk-label">Nouveau mot de passe</label>
                            <div class="relative">
                                <input type="password" name="password" id="password-pc" required
                                       class="login-desk-input pr-12 w-full" placeholder="Nouveau mot de passe" autocomplete="new-password">
                                <button type="button" onclick="togglePassword('password-pc', this)" class="absolute right-0 top-1/2 -translate-y-1/2 p-2 text-white/40 hover:text-[#00d4ff] rounded-lg transition-colors" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
                                    <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="eye-closed hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="password-confirm-pc" class="login-desk-label">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" id="password-confirm-pc" required
                                   class="login-desk-input w-full" placeholder="Confirmer le mot de passe" autocomplete="new-password">
                        </div>
                        <button type="submit" class="login-desk-btn">Réinitialiser le mot de passe</button>
                    </form>
                    <p class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-[#00d4ff] hover:underline">Retour à la connexion</a>
                    </p>
                    <div class="login-desk-legal mt-6">
                        <a href="{{ route('legal.contrat') }}">Contrat</a>
                        <span>·</span>
                        <a href="{{ route('legal.reglement') }}">Règlement</a>
                        <span>·</span>
                        <a href="{{ route('legal.rgpd') }}">RGPD</a>
                        <span>·</span>
                        <a href="{{ route('legal.confidentialite') }}">Politique de Confidentialité</a>
                        <span>·</span>
                        <a href="{{ route('legal.mentions') }}">Mentions Légales</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePassword(inputId, btn) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var open = btn.querySelector('.eye-open');
        var closed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            if (open) open.classList.add('hidden');
            if (closed) closed.classList.remove('hidden');
        } else {
            input.type = 'password';
            if (open) open.classList.remove('hidden');
            if (closed) closed.classList.add('hidden');
        }
    }
    document.querySelectorAll('input[name="code"]').forEach(function(inp) {
        inp.addEventListener('input', function() { this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 8); });
    });
    </script>
</body>
</html>
