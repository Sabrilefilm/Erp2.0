@extends('layouts.app')

@section('title', 'Bonnes actions')

@push('styles')
<style>
/* ── Page Bonnes actions : cohérent dashboard / récompenses / score intégrité ── */
.sf-page { max-width: 56rem; margin: 0 auto; padding-bottom: 2rem; }

/* Hero */
.sf-hero {
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(34,197,94,0.18) 0%, rgba(20,184,166,0.12) 40%, rgba(6,182,212,0.08) 100%);
    border: 1px solid rgba(34,197,94,0.2);
    padding: 24px 28px 28px;
    position: relative;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}
.sf-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #22c55e, #14b8a6, #06b6d4);
    border-radius: 20px 20px 0 0;
    opacity: 0.9;
}
.sf-hero-icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    background: rgba(34,197,94,0.25);
    border: 1px solid rgba(34,197,94,0.35);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(34,197,94,0.2);
}
.sf-hero-title { font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
.sf-hero-sub { font-size: 0.8125rem; color: #94a3b8; margin-top: 4px; }
.sf-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
    padding: 6px 12px;
    border-radius: 9999px;
    background: rgba(34,197,94,0.15);
    border: 1px solid rgba(34,197,94,0.3);
    color: #86efac;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.02em;
}

/* Section titre */
.sf-section-title {
    font-size: 12px;
    font-weight: 700;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 12px;
}

/* Section « Comment gagner » — animations élégantes */
.sf-gain-section .sf-section-title {
    font-size: 10px;
    margin-bottom: 8px;
    opacity: 0;
    animation: sf-gain-fade-in 0.5s ease-out 0.1s forwards;
}
.sf-gain-section .sf-gain-card {
    padding: 10px 12px;
    gap: 10px;
    border-radius: 12px;
    opacity: 0;
    transform: translateY(16px);
    animation: sf-gain-card-fade 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}
.sf-gain-section .grid .sf-gain-card:nth-child(1) { animation-delay: 0.08s; }
.sf-gain-section .grid .sf-gain-card:nth-child(2) { animation-delay: 0.16s; }
.sf-gain-section .grid .sf-gain-card:nth-child(3) { animation-delay: 0.24s; }
.sf-gain-section .grid .sf-gain-card:nth-child(4) { animation-delay: 0.32s; }
.sf-gain-section .grid .sf-gain-card:nth-child(5) { animation-delay: 0.4s; }
@keyframes sf-gain-fade-in {
    to { opacity: 1; }
}
@keyframes sf-gain-card-fade {
    to { opacity: 1; transform: translateY(0); }
}
.sf-gain-section .sf-gain-card::after { border-radius: 12px 12px 0 0; }
.sf-gain-section .sf-gain-label { font-size: 0.8125rem; }
.sf-gain-section .sf-gain-desc { font-size: 0.75rem; margin-top: 0; }

/* Cartes « Comment gagner » — style + animations douces et belles */
.sf-gain-card {
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    padding: 16px 18px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: border-color 0.35s ease, transform 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.35s ease;
    position: relative;
    overflow: hidden;
}
.sf-gain-card:hover {
    border-color: rgba(255,255,255,0.15);
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.2);
}
.sf-gain-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    border-radius: 16px 16px 0 0;
    opacity: 0.85;
    transition: opacity 0.35s ease;
}
.sf-gain-card:hover::after {
    opacity: 1;
}
.sf-gain-1::after { background: linear-gradient(90deg, #22c55e, transparent); }
.sf-gain-2::after { background: linear-gradient(90deg, #0ea5e9, transparent); }
.sf-gain-3::after { background: linear-gradient(90deg, #a78bfa, transparent); }
.sf-gain-4::after { background: linear-gradient(90deg, #f59e0b, transparent); }
.sf-gain-5::after { background: linear-gradient(90deg, #ec4899, transparent); }
.sf-gain-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 40%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
    transition: left 0.6s ease;
}
.sf-gain-card:hover::before {
    left: 100%;
}
.sf-gain-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
    transition: transform 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.35s ease;
}
.sf-gain-card:hover .sf-gain-icon {
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.sf-gain-1 .sf-gain-icon { background: rgba(34,197,94,0.2); }
.sf-gain-2 .sf-gain-icon { background: rgba(14,165,233,0.2); }
.sf-gain-3 .sf-gain-icon { background: rgba(167,139,250,0.2); }
.sf-gain-4 .sf-gain-icon { background: rgba(245,158,11,0.2); }
.sf-gain-5 .sf-gain-icon { background: rgba(236,72,153,0.2); }
.sf-gain-label {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #fff;
    transition: color 0.25s ease;
}
.sf-gain-card:hover .sf-gain-label {
    color: #f1f5f9;
}
.sf-gain-desc {
    font-size: 0.8125rem;
    color: #94a3b8;
    margin-top: 2px;
    transition: color 0.25s ease;
}
.sf-gain-card:hover .sf-gain-desc {
    color: #cbd5e1;
}

/* Bloc récompenses (paliers) — plus petit */
.sf-recompenses-section .sf-section-title {
    font-size: 10px;
    margin-bottom: 6px;
}
.sf-recompenses-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.sf-palier {
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    padding: 10px 12px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.sf-palier:hover { border-color: rgba(34,197,94,0.2); box-shadow: 0 4px 12px rgba(34,197,94,0.08); }
.sf-palier::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    opacity: 0.85;
}
.sf-palier-80::before { background: linear-gradient(90deg, #22c55e, #14b8a6); }
.sf-palier-100::before { background: linear-gradient(90deg, #00ff88, #22c55e); }
.sf-palier-emoji { font-size: 1.25rem; margin-bottom: 2px; }
.sf-palier-points { font-size: 0.8125rem; font-weight: 700; color: #86efac; }
.sf-palier-montant { font-size: 1.125rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
.sf-palier-desc { font-size: 10px; color: #94a3b8; margin-top: 0; }

/* Cadenas sur paliers non disponibles */
.sf-palier-lock {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(100, 116, 139, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #94a3b8;
    pointer-events: none;
}
.sf-palier-lock-icon {
    width: 16px;
    height: 16px;
}

/* Paliers non disponibles = gris */
.sf-palier:not(.sf-palier--available) {
    opacity: 0.55;
    filter: grayscale(0.6);
}
.sf-palier:not(.sf-palier--available) .sf-palier-points,
.sf-palier:not(.sf-palier--available) .sf-palier-montant {
    color: #64748b;
}
.sf-palier:not(.sf-palier--available) .sf-palier-desc {
    color: #475569;
}
.sf-palier:not(.sf-palier--available)::before {
    opacity: 0.4;
    background: linear-gradient(90deg, #64748b, #475569);
}

/* Palier disponible = couleurs + animations (entrée + boucle) */
.sf-palier--available {
    animation: sf-palier-available 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards,
               sf-palier-pulse 2.5s ease-in-out 1s infinite;
}
@keyframes sf-palier-available {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); transform: scale(0.96); opacity: 0.9; }
    40% { box-shadow: 0 0 24px 6px rgba(34, 197, 94, 0.35); transform: scale(1.02); opacity: 1; }
    70% { box-shadow: 0 0 12px 2px rgba(34, 197, 94, 0.2); transform: scale(1); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); transform: scale(1); opacity: 1; }
}
@keyframes sf-palier-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.15); border-color: rgba(34, 197, 94, 0.25); }
    50% { box-shadow: 0 0 16px 2px rgba(34, 197, 94, 0.2); border-color: rgba(34, 197, 94, 0.4); }
}
.sf-palier--available::before {
    animation: sf-palier-bar-glow 2.5s ease-in-out infinite;
}
@keyframes sf-palier-bar-glow {
    0%, 100% { opacity: 0.85; }
    50% { opacity: 1; }
}
.sf-palier.sf-palier--available .sf-palier-emoji {
    animation: sf-palier-emoji-pop 0.6s ease-out 0.2s both,
               sf-palier-emoji-float 2s ease-in-out 1s infinite;
}
@keyframes sf-palier-emoji-pop {
    0% { transform: scale(0.6); opacity: 0; }
    60% { transform: scale(1.15); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes sf-palier-emoji-float {
    0%, 100% { transform: scale(1) translateY(0); }
    50% { transform: scale(1.05) translateY(-3px); }
}
.sf-palier.sf-palier--available .sf-palier-points {
    animation: sf-palier-text-glow 2s ease-in-out 0.8s infinite;
}
@keyframes sf-palier-text-glow {
    0%, 100% { text-shadow: 0 0 8px rgba(134, 239, 172, 0.3); }
    50% { text-shadow: 0 0 14px rgba(134, 239, 172, 0.5); }
}

/* Score du mois — widget plus gros à côté du hero (jauge + chiffre) + animations */
.sf-score-inline {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 0;
    animation: sf-score-inline-enter 0.7s cubic-bezier(0.22,0.68,0,1) 0.15s forwards;
    opacity: 0;
}
@keyframes sf-score-inline-enter {
    0% { opacity: 0; transform: scale(0.7) rotate(-8deg); }
    60% { opacity: 1; transform: scale(1.05) rotate(2deg); }
    100% { opacity: 1; transform: scale(1) rotate(0deg); }
}
.sf-score-inline-ring {
    position: relative;
    width: 120px;
    height: 120px;
    flex-shrink: 0;
    animation: sf-ring-float 3s ease-in-out infinite;
}
@keyframes sf-ring-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}
.sf-score-inline-ring svg {
    width: 100%;
    height: 100%;
    display: block;
    transform: rotate(-90deg);
    filter: drop-shadow(0 0 12px rgba(34,197,94,0.25));
}
.sf-score-inline-ring .ring-bg {
    fill: none;
    stroke: rgba(255,255,255,0.12);
    stroke-width: 8;
    stroke-linecap: round;
}
.sf-score-inline-ring .ring-fill {
    fill: none;
    stroke: url(#sf-score-inline-gradient);
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 289 289;
    stroke-dashoffset: 289;
    animation: sf-inline-ring-fill 1.1s cubic-bezier(0.22,0.68,0,1) 0.3s forwards;
}
@keyframes sf-inline-ring-fill {
    to { stroke-dashoffset: var(--stroke-offset-end, 289); }
}
.sf-score-inline-value-wrap {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0;
}
.sf-score-inline-value {
    font-size: 2.25rem;
    font-weight: 900;
    line-height: 1;
    letter-spacing: -0.04em;
    background: linear-gradient(135deg, #86efac 0%, #22c55e 40%, #00ff88 100%);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    animation: sf-inline-pop 0.6s cubic-bezier(0.22,0.68,0,1) 0.5s forwards, sf-glow-pulse 2.2s ease-in-out 1.2s infinite;
    opacity: 0;
    transform: scale(0.5);
    filter: drop-shadow(0 0 16px rgba(34,197,94,0.4));
}
@keyframes sf-inline-pop {
    0% { opacity: 0; transform: scale(0.5); }
    70% { opacity: 1; transform: scale(1.12); }
    100% { opacity: 1; transform: scale(1); }
}
@keyframes sf-glow-pulse {
    0%, 100% { filter: drop-shadow(0 0 16px rgba(34,197,94,0.4)); }
    50% { filter: drop-shadow(0 0 24px rgba(34,197,94,0.65)); }
}
.sf-score-inline-max {
    font-size: 0.8125rem;
    font-weight: 700;
    color: rgba(255,255,255,0.5);
    margin-top: 2px;
    line-height: 1.2;
}
.sf-score-inline-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-left: 14px;
    padding-left: 14px;
    border-left: 2px solid rgba(34,197,94,0.35);
    animation: sf-label-fade 0.5s ease-out 0.8s forwards;
    opacity: 0;
}
@keyframes sf-label-fade {
    to { opacity: 1; }
}
.sf-hero-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px 24px;
}
.sf-hero-content { flex: 1; min-width: 0; }
.sf-hero-score { flex-shrink: 0; }
@keyframes sf-fade-in {
    to { opacity: 1; }
}

/* Bloc générique */
.sf-block {
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    overflow: hidden;
}
.sf-block-title {
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    font-size: 0.9375rem;
    font-weight: 600;
    color: #fff;
}
.sf-block-body { padding: 16px 20px; }
.sf-action-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    font-size: 0.8125rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    flex-wrap: wrap;
}
.sf-action-row:last-child { border-bottom: 0; }

</style>
@endpush

@section('content')
<div class="sf-page space-y-6">
    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-sm px-4 py-3 flex items-center gap-2">
        <span aria-hidden="true">✓</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Hero : titre + mois + score (petit) à côté ── --}}
    @if($createur && $scoreFidelite)
    @php
        $score = $scoreFidelite->score;
        $max = \App\Models\ScoreFidelite::SCORE_MAX;
        $pct = min(100, (int) round(($score / $max) * 100));
        $circumferenceInline = 2 * M_PI * 46;
        $strokeOffsetEndInline = $circumferenceInline * (1 - $pct / 100);
    @endphp
    @endif
    <div class="sf-hero">
        <div class="sf-hero-row">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="sf-hero-icon" aria-hidden="true">🎁</div>
                <div class="sf-hero-content">
                    <h1 class="sf-hero-title">Bonnes actions</h1>
                @if(isset($moisLabel))
                <p class="sf-hero-sub">{{ $moisLabel }} — Score remis à zéro le 1er du mois.</p>
                @else
                <p class="sf-hero-sub">Score du mois — remis à zéro le 1er de chaque mois.</p>
                @endif
                <p class="sf-hero-sub mt-1">Participe aux matchs, réunions et entraide : cumule des points et débloque des récompenses.</p>
                    <div class="sf-hero-badge">
                        <span aria-hidden="true">✨</span>
                        <span>Chaque action compte</span>
                    </div>
                </div>
            </div>
            @if($createur && $scoreFidelite)
            <div class="sf-hero-score">
                <div class="sf-score-inline">
                    <div class="sf-score-inline-ring">
                        <svg viewBox="0 0 120 120" aria-hidden="true">
                            <defs>
                                <linearGradient id="sf-score-inline-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#22c55e"/>
                                    <stop offset="50%" stop-color="#00ff88"/>
                                    <stop offset="100%" stop-color="#06b6d4"/>
                                </linearGradient>
                            </defs>
                            <circle class="ring-bg" cx="60" cy="60" r="46"/>
                            <circle class="ring-fill" cx="60" cy="60" r="46" style="--stroke-offset-end: {{ number_format($strokeOffsetEndInline, 2, '.', '') }}"/>
                        </svg>
                        <div class="sf-score-inline-value-wrap">
                            <span class="sf-score-inline-value">{{ $score }}</span>
                            <span class="sf-score-inline-max">/ {{ $max }}</span>
                        </div>
                    </div>
                    <span class="sf-score-inline-label">Ce mois</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Comment gagner des points (plus petit) ── --}}
    <div class="sf-gain-section">
        <h2 class="sf-section-title">Comment gagner des points</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="sf-gain-card sf-gain-1">
                <div class="sf-gain-icon">⚽</div>
                <div>
                    <p class="sf-gain-label">Matchs officiels</p>
                    <p class="sf-gain-desc">Sois actif et participe aux matchs. Chaque match honoré compte.</p>
                </div>
            </div>
            <div class="sf-gain-card sf-gain-2">
                <div class="sf-gain-icon">👥</div>
                <div>
                    <p class="sf-gain-label">Réunions</p>
                    <p class="sf-gain-desc">Participe aux réunions de l'agence.</p>
                </div>
            </div>
            <div class="sf-gain-card sf-gain-3">
                <div class="sf-gain-icon">🤝</div>
                <div>
                    <p class="sf-gain-label">Entraide</p>
                    <p class="sf-gain-desc">Aide et conseille les créateurs.</p>
                </div>
            </div>
            <div class="sf-gain-card sf-gain-4">
                <div class="sf-gain-icon">➕</div>
                <div>
                    <p class="sf-gain-label">Parrainage</p>
                    <p class="sf-gain-desc">Invite de nouveaux créateurs.</p>
                </div>
            </div>
            <div class="sf-gain-card sf-gain-5 md:col-span-2">
                <div class="sf-gain-icon">📈</div>
                <div>
                    <p class="sf-gain-label">Évolution de concept</p>
                    <p class="sf-gain-desc">Améliore ton contenu et ton projet.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Récompenses (paliers 80 / 100) — gris si non dispo, animation si dispo ── --}}
    @php
        $palier80Ok = $createur && $scoreFidelite && $scoreFidelite->palier_80_debloque_at !== null;
        $palier100Ok = $createur && $scoreFidelite && $scoreFidelite->palier_100_debloque_at !== null;
    @endphp
    <div class="sf-recompenses-section">
        <h2 class="sf-section-title">Récompenses du mois</h2>
        <div class="sf-recompenses-wrap">
            <div class="sf-palier sf-palier-80 {{ $palier80Ok ? 'sf-palier--available' : '' }}">
                @if(!$palier80Ok)
                <span class="sf-palier-lock" aria-hidden="true" title="Verrouillé — atteignez 80 points pour débloquer">
                    <svg class="sf-palier-lock-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                @endif
                <div class="sf-palier-emoji" aria-hidden="true">🎟️</div>
                <p class="sf-palier-points">80 points</p>
                <p class="sf-palier-desc">Avantages exclusifs</p>
            </div>
            <div class="sf-palier sf-palier-100 {{ $palier100Ok ? 'sf-palier--available' : '' }}">
                @if(!$palier100Ok)
                <span class="sf-palier-lock" aria-hidden="true" title="Verrouillé — atteignez 100 points pour débloquer">
                    <svg class="sf-palier-lock-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                @endif
                <div class="sf-palier-emoji" aria-hidden="true">🏆</div>
                <p class="sf-palier-points">100 points</p>
                <p class="sf-palier-desc">Gagne des cadeaux 5/10/15/20 euro</p>
            </div>
        </div>
    </div>

    @if($createur && $scoreFidelite && $recentActions->isNotEmpty())
    <div class="sf-block">
        <div class="sf-block-title">Dernières actions</div>
        <div class="sf-block-body p-0">
            <div class="divide-y divide-white/5">
                @foreach($recentActions as $a)
                @php
                    $isRemove = $a->action_type === \App\Models\ScoreFidelite::ACTION_MANUAL_REMOVE;
                    $isSet = $a->action_type === \App\Models\ScoreFidelite::ACTION_MANUAL_SET;
                    $pointsDisplay = $isSet ? '→ '.$a->points : ($isRemove ? '-'.$a->points : '+'.$a->points);
                    $pointsClass = $isRemove ? 'text-rose-400' : 'text-emerald-400';
                @endphp
                <div class="sf-action-row">
                    <span class="{{ $pointsClass }} font-semibold tabular-nums">{{ $pointsDisplay }}</span>
                    <span class="text-[#94a3b8]">{{ \App\Models\ScoreFidelite::actionLabels()[$a->action_type] ?? $a->action_type }}</span>
                    @if(!empty($a->raison))
                    <span class="text-white/70 text-xs">— {{ $a->raison }}</span>
                    @endif
                    <span class="text-white/50 text-xs ml-auto">{{ $a->created_at?->translatedFormat('d/m/Y H:i') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Gestion (fondateur / agent / manageur) ── --}}
    @if(!empty($showGestion))
    <div class="sf-block">
        <div class="sf-block-title">Ajouter ou retirer des points (mois en cours)</div>
        <div class="sf-block-body">
            @if($createursAvecScore->isNotEmpty())
            <form action="{{ route('score-fidelite.update-score') }}" method="POST" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="min-w-[200px]">
                    <label for="createur_id" class="block text-xs font-medium text-[#94a3b8] mb-1">Créateur</label>
                    <select name="createur_id" id="createur_id" required class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/30">
                        <option value="">Choisir un créateur</option>
                        @foreach($createursAvecScore as $row)
                        <option value="{{ $row->createur->id }}">{{ $row->createur->nom }} ({{ $row->score }}/100)</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label for="action" class="block text-xs font-medium text-[#94a3b8] mb-1">Action</label>
                    <select name="action" id="action" required class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm">
                        <option value="add">Ajouter des points</option>
                        <option value="remove">Retirer des points</option>
                        <option value="set">Mettre le score à 0</option>
                    </select>
                </div>
                <div class="w-20">
                    <label for="points" class="block text-xs font-medium text-[#94a3b8] mb-1">Nombre</label>
                    <input type="number" name="points" id="points" min="0" max="100" value="0" required class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm">
                </div>
                <div class="min-w-[200px] flex-1">
                    <label for="raison" class="block text-xs font-medium text-[#94a3b8] mb-1">Raison ou note (optionnel)</label>
                    <input type="text" name="raison" id="raison" placeholder="Ex. Rattrapage match, correction…" maxlength="255"
                           class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/30"
                           value="{{ old('raison') }}">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition-colors">Appliquer</button>
            </form>
            @else
            <p class="text-sm text-[#94a3b8]">Aucun créateur dans ton périmètre. Importe des créateurs (menu <strong>Import</strong>) ou vérifie qu'il existe au moins un créateur pour pouvoir modifier les scores.</p>
            @endif
        </div>
    </div>
    @if($createursAvecScore->isNotEmpty())
    <div class="sf-block">
        <div class="sf-block-title">Scores du mois par créateur</div>
        <div class="sf-block-body p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[#94a3b8] border-b border-white/10 bg-white/5">
                            <th class="py-3 px-4 font-medium">Créateur</th>
                            <th class="py-3 px-4 font-medium">Score</th>
                            <th class="py-3 px-4 font-medium">15 € (80 points)</th>
                            <th class="py-3 px-4 font-medium">35 € (100 points)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($createursAvecScore as $row)
                        <tr class="border-b border-white/5 text-white/90 hover:bg-white/[0.03]">
                            <td class="py-3 px-4 font-medium">{{ $row->createur->nom }}</td>
                            <td class="py-3 px-4 tabular-nums">{{ $row->score }}/100</td>
                            <td class="py-3 px-4">@if($row->palier_80)<span class="text-emerald-400">✓</span>@else—@endif</td>
                            <td class="py-3 px-4">@if($row->palier_100)<span class="text-emerald-400">✓</span>@else—@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
