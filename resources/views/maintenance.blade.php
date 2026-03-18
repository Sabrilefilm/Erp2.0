<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance en cours — Ultra</title>
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
            background: linear-gradient(135deg, #060a10 0%, #0d1520 40%, #0e1219 80%, #060a10 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #fff;
            overflow-x: hidden;
            padding: 1.5rem;
        }
        .scene {
            position: relative;
            width: 200px;
            height: 140px;
            margin-bottom: 2rem;
        }
        .robot {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 90px;
            animation: float 3s ease-in-out infinite;
        }
        .robot-head {
            width: 56px;
            height: 56px;
            margin: 0 auto 4px;
            background: linear-gradient(180deg, #64748b 0%, #475569 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #94a3b8;
            box-shadow: inset 0 -4px 0 rgba(0,0,0,0.2);
        }
        .robot-eyes {
            display: flex;
            gap: 12px;
        }
        .robot-eye {
            width: 10px;
            height: 10px;
            background: #22d3ee;
            border-radius: 50%;
            animation: blink 4s ease-in-out infinite;
            box-shadow: 0 0 12px #22d3ee;
        }
        .robot-body {
            width: 52px;
            height: 32px;
            margin: 0 auto;
            background: linear-gradient(180deg, #475569 0%, #334155 100%);
            border-radius: 12px;
            border: 2px solid #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .robot-body::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background: #22d3ee;
            border-radius: 2px;
            animation: glow 1.5s ease-in-out infinite;
        }
        .tool {
            position: absolute;
            font-size: 1.5rem;
            animation: bounce 2s ease-in-out infinite;
        }
        .tool-1 { top: 10%; left: 5%; animation-delay: 0s; }
        .tool-2 { top: 5%; right: 15%; animation-delay: 0.3s; }
        .tool-3 { bottom: 25%; left: 10%; animation-delay: 0.6s; }
        .tool-4 { bottom: 20%; right: 5%; animation-delay: 0.9s; }
        .tool-5 { top: 40%; left: -5%; animation-delay: 0.2s; }
        .tool-6 { top: 35%; right: -5%; animation-delay: 0.5s; }
        @keyframes float {
            0%, 100% { transform: translate(-50%, -50%) translateY(0); }
            50% { transform: translate(-50%, -50%) translateY(-8px); }
        }
        @keyframes blink {
            0%, 45%, 55%, 100% { transform: scaleY(1); opacity: 1; }
            50% { transform: scaleY(0.1); opacity: 0.8; }
        }
        @keyframes glow {
            0%, 100% { opacity: 0.6; box-shadow: 0 0 4px #22d3ee; }
            50% { opacity: 1; box-shadow: 0 0 12px #22d3ee; }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }
        .wrap {
            text-align: center;
            max-width: 420px;
        }
        h1 {
            font-size: clamp(1.5rem, 4vw, 1.85rem);
            font-weight: 800;
            color: #e2e8f0;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .msg {
            font-size: 1.05rem;
            color: #94a3b8;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .progress-fun {
            width: 100%;
            max-width: 280px;
            height: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 999px;
            margin: 0 auto 1.75rem;
            overflow: hidden;
        }
        .progress-fun-inner {
            height: 100%;
            width: 30%;
            background: linear-gradient(90deg, #22d3ee, #6366f1);
            border-radius: 999px;
            animation: progress-run 2.5s ease-in-out infinite;
        }
        @keyframes progress-run {
            0% { width: 10%; margin-left: 0; }
            50% { width: 70%; margin-left: 15%; }
            100% { width: 10%; margin-left: 0; }
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
        }
        .btn:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 28px rgba(99, 102, 241, 0.5);
        }
        .foot-note {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="scene" aria-hidden="true">
            <span class="tool tool-1">🔧</span>
            <span class="tool tool-2">🪛</span>
            <span class="tool tool-3">⚙️</span>
            <span class="tool tool-4">🔩</span>
            <span class="tool tool-5">✨</span>
            <span class="tool tool-6">✨</span>
            <div class="robot">
                <div class="robot-head">
                    <div class="robot-eyes">
                        <span class="robot-eye"></span>
                        <span class="robot-eye"></span>
                    </div>
                </div>
                <div class="robot-body"></div>
            </div>
        </div>
        <h1>On bricole par ici ! 🛠️</h1>
        <p class="msg">Notre petit robot met tout en ordre. On revient très vite, promis.</p>
        <div class="progress-fun">
            <div class="progress-fun-inner"></div>
        </div>
        <a href="{{ route('login') }}" class="btn">Fondateur : accéder au site</a>
        <p class="foot-note">Service temporairement indisponible — Merci de votre patience</p>
    </div>
</body>
</html>
