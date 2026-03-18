<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mot de passe oublié — Ultra</title>
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
            <h1 class="login-title">Mot de passe oublié</h1>
            <p class="text-sm text-white/60 mb-4">Indiquez votre adresse e-mail pour recevoir un lien de réinitialisation.</p>

            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->has('email'))
                <p class="mb-2 text-sm text-red-400">{{ $errors->first('email') }}</p>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="login-input-wrap">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="login-input" placeholder="Adresse e-mail" autocomplete="email">
                </div>
                <button type="submit" class="login-btn">Envoyer le lien</button>
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
                    <h1 class="login-desk-title">Mot de passe oublié</h1>
                    <p class="login-desk-desc">
                        Indiquez l’e-mail de votre compte pour recevoir un lien de réinitialisation.
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
                    <h2 class="login-title">Mot de passe oublié</h2>
                    <p class="text-sm text-white/50 text-center -mt-2 mb-6">Entrez votre adresse e-mail pour recevoir le lien.</p>

                    @if (session('status'))
                        <div class="mb-4 p-3 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->has('email'))
                        <p class="mb-2 text-sm text-red-400">{{ $errors->first('email') }}</p>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="email-pc" class="login-desk-label">Adresse e-mail</label>
                            <input type="email" name="email" id="email-pc" value="{{ old('email') }}" required autofocus
                                   class="login-desk-input w-full" placeholder="vous@exemple.com" autocomplete="email">
                        </div>
                        <button type="submit" class="login-desk-btn">Envoyer le lien</button>
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
</body>
</html>
