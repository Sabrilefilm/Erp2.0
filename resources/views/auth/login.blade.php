<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — Ultra</title>
    @include('partials.head-assets')
    <link rel="stylesheet" href="{{ asset('css/login-modern.css') }}">
</head>
<body class="min-h-screen bg-[#0a0e27]">
    {{-- ========== MOBILE ========== --}}
    <div class="lg:hidden login-wrap">
        <div class="login-bg" aria-hidden="true"></div>
        <div class="login-orb login-orb-1" aria-hidden="true"></div>
        <div class="login-orb login-orb-2" aria-hidden="true"></div>
        <div class="login-orb login-orb-3" aria-hidden="true"></div>
        <span class="login-hero-text" aria-hidden="true">Bienvenue</span>

        <div class="login-card">
            <p class="login-app-name">Ultra</p>
            <h1 class="login-title">Bienvenue !</h1>

            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-400 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="remember" value="1">
                <div class="login-input-wrap">
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                           class="login-input" placeholder="Utilisateur" style="padding-right: 16px;" autocomplete="username">
                    @error('username')
                        <p class="login-error-msg">{{ $message }}</p>
                    @enderror
                </div>
                @if(!empty($showCaptcha) && isset($captchaNum1) && isset($captchaNum2))
                <div class="login-input-wrap">
                    <label for="captcha_answer" class="block text-sm text-white/70 mb-1">Combien font {{ $captchaNum1 }} + {{ $captchaNum2 }} ?</label>
                    <input type="number" name="captcha_answer" id="captcha_answer" required min="0" step="1" class="login-input" placeholder="Réponse" autocomplete="off">
                    @error('captcha_answer')
                        <p class="login-error-msg">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                <div class="login-input-wrap">
                    <input type="password" name="password" id="password" required class="login-input" placeholder="Mot de passe">
                    <button type="button" onclick="togglePassword('password', this)" class="login-eye" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
                        <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-closed hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>

                <a href="{{ route('password.reset-with-code.form') }}" class="login-forgot">Mot de passe oublié ?</a>
                <button type="submit" class="login-btn">Connexion</button>
            </form>

            <p class="login-footer">Pas de compte ? <a href="mailto:admin@agence.local">Contactez l'administrateur</a></p>
            <div class="login-legal">
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

    {{-- ========== DESKTOP ========== --}}
    <div class="hidden lg:flex min-h-screen w-full">
        <div class="hidden lg:flex lg:flex-1 lg:min-w-0 lg:max-w-[48%] login-desk-left">
            <div class="login-desk-orb login-desk-orb-1" aria-hidden="true"></div>
            <div class="login-desk-orb login-desk-orb-2" aria-hidden="true"></div>
            <div class="login-desk-content flex flex-col justify-between p-12 xl:p-20 w-full">
                <div>
                    <div class="login-desk-logo">U</div>
                    <p class="login-desk-tag">Ultra</p>
                    <h1 class="login-desk-title">Bienvenue !</h1>
                    <p class="login-desk-desc">
                        Gère tes équipes, créateurs et statistiques en un seul endroit. Accès réservé aux membres de l'agence.
                    </p>
                </div>
                <div>
                    <p class="login-desk-copy">© {{ date('Y') }} Ultra. Tous droits réservés.</p>
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
                    <h2 class="login-title">Connexion</h2>
                    <p class="text-sm text-white/50 text-center -mt-2 mb-8">Accès réservé aux membres de l'agence.</p>

                    @if (session('status'))
                        <div class="mb-4 p-3 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-3 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-400 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="username-pc" class="login-desk-label">Utilisateur</label>
                            <input type="text" name="username" id="username-pc" value="{{ old('username') }}" required autofocus
                                   class="login-desk-input" placeholder="Nom d'utilisateur" autocomplete="username">
                            @error('username')
                                <p class="login-error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                        @if(!empty($showCaptcha) && isset($captchaNum1) && isset($captchaNum2))
                        <div>
                            <label for="captcha_answer-pc" class="login-desk-label">Combien font {{ $captchaNum1 }} + {{ $captchaNum2 }} ?</label>
                            <input type="number" name="captcha_answer" id="captcha_answer-pc" required min="0" step="1" class="login-desk-input" placeholder="Réponse" autocomplete="off">
                            @error('captcha_answer')
                                <p class="login-error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                        <div>
                            <label for="password-pc" class="login-desk-label">Mot de passe</label>
                            <div class="relative">
                                <input type="password" name="password" id="password-pc" required
                                       class="login-desk-input pr-12" placeholder="Mot de passe">
                                <button type="button" onclick="togglePassword('password-pc', this)" class="absolute right-0 top-1/2 -translate-y-1/2 p-2 text-white/40 hover:text-[#00d4ff] rounded-lg transition-colors" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
                                    <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="eye-closed hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <p class="mt-1.5 text-right"><a href="{{ route('password.reset-with-code.form') }}" class="login-desk-forgot">Mot de passe oublié ?</a></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="remember" id="remember-pc" class="h-4 w-4 rounded border-white/20 bg-white/10 text-[#00d4ff] focus:ring-[#00d4ff]">
                            <label for="remember-pc" class="login-desk-remember-label">Se souvenir de moi</label>
                        </div>
                        <button type="submit" class="login-desk-btn">Connexion</button>
                    </form>
                    <div class="login-desk-legal">
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
            btn.setAttribute('title', 'Masquer le mot de passe');
            btn.setAttribute('aria-label', 'Masquer le mot de passe');
        } else {
            input.type = 'password';
            if (open) open.classList.remove('hidden');
            if (closed) closed.classList.add('hidden');
            btn.setAttribute('title', 'Afficher le mot de passe');
            btn.setAttribute('aria-label', 'Afficher le mot de passe');
        }
    }
    </script>
</body>
</html>
