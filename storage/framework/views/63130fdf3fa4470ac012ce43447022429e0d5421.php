<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page introuvable</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #fff;
            overflow-x: hidden;
            padding: 1.5rem;
        }
        .err-wrap {
            text-align: center;
            position: relative;
        }
        .err-emojis {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .err-emoji {
            font-size: 3.5rem;
            animation: float 2.5s ease-in-out infinite;
        }
        .err-emoji:nth-child(1) { animation-delay: 0s; }
        .err-emoji:nth-child(2) { animation-delay: 0.2s; }
        .err-emoji:nth-child(3) { animation-delay: 0.4s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-14px) rotate(5deg); }
        }
        .err-code {
            font-size: clamp(6rem, 20vw, 10rem);
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #f472b6 0%, #a78bfa 50%, #38bdf8 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-shift 4s ease infinite;
            letter-spacing: -0.04em;
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .err-label {
            font-size: clamp(1.25rem, 4vw, 1.75rem);
            font-weight: 600;
            color: #94a3b8;
            margin-top: 0.5rem;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .err-msg {
            font-size: 1rem;
            color: #64748b;
            max-width: 320px;
            margin: 1.25rem auto 0;
        }
        .err-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            padding: 0.875rem 1.75rem;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            color: #fff;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
            animation: btn-glow 2s ease-in-out infinite;
        }
        .err-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 28px rgba(99, 102, 241, 0.5);
        }
        .err-btn:active {
            transform: scale(0.98);
        }
        @keyframes btn-glow {
            0%, 100% { box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4); }
            50% { box-shadow: 0 4px 28px rgba(139, 92, 246, 0.5); }
        }
        .err-btn svg {
            width: 1.25rem;
            height: 1.25rem;
            animation: arrow-wiggle 1s ease-in-out infinite;
        }
        @keyframes arrow-wiggle {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(4px); }
        }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            pointer-events: none;
            animation: confetti-fall 6s linear infinite;
        }
        .confetti:nth-child(1) { left: 10%; background: #f472b6; animation-delay: 0s; animation-duration: 5s; }
        .confetti:nth-child(2) { left: 20%; background: #a78bfa; animation-delay: 0.5s; animation-duration: 6s; }
        .confetti:nth-child(3) { left: 30%; background: #38bdf8; animation-delay: 1s; animation-duration: 5.5s; }
        .confetti:nth-child(4) { left: 50%; background: #34d399; animation-delay: 0.2s; animation-duration: 6.5s; }
        .confetti:nth-child(5) { left: 70%; background: #fbbf24; animation-delay: 1.2s; animation-duration: 5s; }
        .confetti:nth-child(6) { left: 80%; background: #fb923c; animation-delay: 0.8s; animation-duration: 6s; }
        .confetti:nth-child(7) { left: 90%; background: #f472b6; animation-delay: 0.3s; animation-duration: 5.8s; }
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0.3;
            }
        }
    </style>
</head>
<body>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>

    <div class="err-wrap">
        <div class="err-emojis">
            <span class="err-emoji">🔍</span>
            <span class="err-emoji">😅</span>
            <span class="err-emoji">📄</span>
        </div>
        <h1 class="err-code">404</h1>
        <p class="err-label">NOT FOUND</p>
        <p class="err-msg">Oups ! Cette page s’est perdue dans le cosmos. Pas de panique, on te ramène à la maison.</p>
        <a href="<?php echo e(url('/')); ?>" class="err-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Retourner à l'accueil
        </a>
    </div>
</body>
</html>
<?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/errors/404.blade.php ENDPATH**/ ?>