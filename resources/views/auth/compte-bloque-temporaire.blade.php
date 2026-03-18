<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Compte temporairement bloqué — Ultra</title>
    @include('partials.head-assets')
    <style>
        body { min-height: 100vh; background: #0a0e27; display: flex; align-items: center; justify-content: center; padding: 24px; font-family: system-ui, sans-serif; }
        .card { max-width: 440px; width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 32px; text-align: center; }
        .icon { width: 64px; height: 64px; margin: 0 auto 20px; border-radius: 16px; background: rgba(245,158,11,0.2); display: flex; align-items: center; justify-content: center; }
        .icon svg { width: 32px; height: 32px; color: #f59e0b; }
        h1 { font-size: 1.35rem; font-weight: 700; color: #fff; margin-bottom: 12px; }
        p { font-size: 0.9375rem; color: #94a3b8; line-height: 1.6; margin-bottom: 24px; }
        .btn-login { display: inline-block; padding: 12px 24px; border-radius: 12px; background: rgba(0,212,255,0.2); border: 1px solid rgba(0,212,255,0.4); color: #67e8f9; font-weight: 600; font-size: 0.9375rem; text-decoration: none; transition: background .2s, color .2s; }
        .btn-login:hover { background: rgba(0,212,255,0.3); color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1>Compte temporairement bloqué</h1>
        <p>Notre compte a été temporairement bloqué après plusieurs tentatives de connexion incorrectes. Merci de prendre contact avec votre fondateur ou votre agent pour débloquer l'accès et réinitialiser votre mot de passe.</p>
        <a href="{{ route('login') }}" class="btn-login">Retour à la connexion</a>
    </div>
</body>
</html>
