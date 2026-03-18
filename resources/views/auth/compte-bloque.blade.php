<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Compte suspendu — Ultra</title>
    @include('partials.head-assets')
    <style>
        body { min-height: 100vh; background: #0a0e27; display: flex; align-items: center; justify-content: center; padding: 24px; font-family: system-ui, sans-serif; }
        .card { max-width: 420px; width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 32px; text-align: center; }
        .icon { width: 64px; height: 64px; margin: 0 auto 20px; border-radius: 16px; background: rgba(239,68,68,0.2); display: flex; align-items: center; justify-content: center; }
        .icon svg { width: 32px; height: 32px; color: #ef4444; }
        h1 { font-size: 1.35rem; font-weight: 700; color: #fff; margin-bottom: 12px; }
        p { font-size: 0.9375rem; color: #94a3b8; line-height: 1.6; margin-bottom: 24px; }
        .btn-logout { display: inline-block; padding: 12px 24px; border-radius: 12px; background: rgba(239,68,68,0.2); border: 1px solid rgba(239,68,68,0.4); color: #f87171; font-weight: 600; font-size: 0.9375rem; text-decoration: none; transition: background .2s, color .2s; }
        .btn-logout:hover { background: rgba(239,68,68,0.3); color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1>Accès suspendu</h1>
        <p>Votre score d'intégrité est à 10&nbsp;% ou moins. L'accès à votre compte a été bloqué automatiquement.<br>Contactez votre agent ou le fondateur pour régulariser votre situation.</p>
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn-logout">Se déconnecter</button>
        </form>
    </div>
</body>
</html>
