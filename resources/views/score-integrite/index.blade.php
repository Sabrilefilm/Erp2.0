@extends('layouts.app')

@section('title', 'Score d\'intégrité actuel')

@push('styles')
<style>
/* Compteur score intégrité – refonte complète */
.si-gauge-wrap {
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.08);
    background: linear-gradient(165deg, rgba(15,23,42,0.6) 0%, rgba(30,41,59,0.4) 100%);
    padding: 28px 24px 32px;
    position: relative;
    overflow: visible;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.06), 0 8px 32px rgba(0,0,0,0.25);
}
.si-gauge-svg {
    display: block;
    margin: 0 auto;
    max-width: 280px;
}
.si-gauge-arc-bg {
    fill: none;
    stroke: rgba(255,255,255,0.08);
    stroke-width: 12;
    stroke-linecap: round;
}
.si-gauge-arc-fill {
    fill: none;
    stroke: url(#si-arc-fill);
    stroke-width: 12;
    stroke-linecap: round;
    transition: stroke-dasharray 0.7s cubic-bezier(0.34, 1.2, 0.64, 1);
}
.si-gauge-tick {
    stroke: rgba(255, 255, 255, 0.64);
    stroke-width: 1.2;
}
.si-gauge-value {
    font-size: 2rem;
    font-weight: 700;
    fill: #fff;
    text-anchor: middle;
    font-family: system-ui, sans-serif;
    letter-spacing: -0.03em;
}
.si-gauge-value-sub {
    font-size: 0.75rem;
    font-weight: 500;
    fill: #64748b;
    text-anchor: middle;
    font-family: system-ui, sans-serif;
    letter-spacing: 0.02em;
}
.si-gauge-scale {
    font-size: 10px;
    font-weight: 600;
    fill: #94a3b8;
    text-anchor: middle;
    font-family: system-ui, sans-serif;
    letter-spacing: 0.02em;
}
.si-gauge-needle {
    fill: #f1f5f9;
    stroke: rgba(0,0,0,0.15);
    stroke-width: 0.6;
}
.si-gauge-hub {
    fill: #e2e8f0;
    stroke: rgba(0,0,0,0.12);
    stroke-width: 1;
}
.si-gauge-dot {
    stroke: rgba(255,255,255,0.6);
    stroke-width: 1;
}
.si-gauge-msg {
    text-align: center;
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 10px;
    letter-spacing: 0.01em;
    color: #94a3b8;
}
.si-gauge-msg.good { color: #0ea5e9; }
.si-gauge-msg.warn { color: #f59e0b; }
.si-gauge-msg.bad { color: #ef4444; }
.si-block {
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    overflow: hidden;
}
.si-block-title {
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    font-size: 0.9375rem;
    font-weight: 600;
    color: #fff;
}
.si-block-body { padding: 20px; }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-8 max-w-4xl mx-auto">
    {{-- Hero --}}
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-emerald-500/20 via-cyan-500/10 to-blue-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-2xl">📊</div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Score d'intégrité actuel</h1>
                <p class="text-[#94a3b8] text-sm mt-0.5">Ta conformité à la Politique de gestion d'agence.</p>
            </div>
        </div>
    </div>

    {{-- Explication : Politique de gestion d'agence --}}
    <div class="si-block">
        <div class="si-block-title">Score d'intégrité actuel – Politique de gestion d'agence</div>
        <div class="si-block-body space-y-4 text-sm text-[#94a3b8] leading-relaxed">
            <p>
                Le <strong class="text-white">score d'intégrité</strong> reflète le niveau de conformité de l'agence par rapport à la <strong class="text-white">Politique de gestion d'agence</strong>.
            </p>
            <p>
                Ce score évolue en fonction des comportements, des actions et du respect des règles par les membres de l'agence.
            </p>
            <p>
                Plus les violations sont graves ou répétées (non-respect des règles, comportements inappropriés, fraudes, etc.), plus le score d'intégrité diminue.
            </p>
            <p>
                En cas de manquements sérieux ou répétés, des sanctions peuvent être appliquées.
            </p>
            <p>
                Si la situation ne s'améliore pas malgré les avertissements, l'agence peut aller jusqu'à l'<strong class="text-white">exclusion définitive</strong> du membre concerné.
            </p>
            <p>
                Le maintien d'un bon score d'intégrité est donc essentiel pour garantir la crédibilité, la stabilité et la pérennité de l'agence.
            </p>
            <p class="pt-2 border-t border-white/10">
                <strong class="text-white">Zones du score :</strong><br>
                <span class="text-red-400">0–30</span> = Rouge (critique ; en dessous de 20 = fermeture du contrat par l'agence).<br>
                <span class="text-amber-400">30–60</span> = Orange (à améliorer).<br>
                <span class="text-sky-400">60–100</span> = Bleu (bon niveau).
            </p>
        </div>
    </div>

    @if($createur)
    {{-- Compteur 0–100 refait : arc + aiguille + valeur --}}
    @php
        $cx = 110;
        $cy = 88;
        $r = 62;
        $halfCircle = M_PI * $r;
        $dashArray = $halfCircle * ($scoreActuel / $scoreMax) . ' ' . $halfCircle;
        // 0-30 rouge (critique, &lt;20 = fermeture contrat) | 30-60 orange | 60-100 bleu
        if ($scoreActuel >= 60) {
            $msg = 'Bien joué ! Continue comme ça.';
            $msgClass = 'good';
            $arcColor = '#0ea5e9';
            $dotColor = '#0ea5e9';
        } elseif ($scoreActuel >= 30) {
            $msg = 'Tu peux mieux faire.';
            $msgClass = 'warn';
            $arcColor = '#f59e0b';
            $dotColor = '#f59e0b';
        } else {
            $msg = 'Attention : score critique. En dessous de 20, risque de fermeture du contrat.';
            $msgClass = 'bad';
            $arcColor = '#ef4444';
            $dotColor = '#ef4444';
        }
        $scaleValues = [0, 20, 40, 60, 80, 100];
        $labelR = 74;
        $tickIn = 66;
        $tickOut = 72;
        $scalePositions = [];
        foreach ($scaleValues as $v) {
            $deg = 180 - ($v / 100) * 180;
            $rad = $deg * M_PI / 180;
            $scalePositions[] = [
                'value' => $v,
                'x' => round($cx + $labelR * cos($rad), 1),
                'y' => round($cy - $labelR * sin($rad), 1),
                't1' => [round($cx + $tickIn * cos($rad), 1), round($cy - $tickIn * sin($rad), 1)],
                't2' => [round($cx + $tickOut * cos($rad), 1), round($cy - $tickOut * sin($rad), 1)],
            ];
        }
        $needleRad = (180 - ($scoreActuel / 100) * 180) * M_PI / 180;
        $tipX = round($cx + $r * cos($needleRad), 2);
        $tipY = round($cy - $r * sin($needleRad), 2);
        $dx = $tipX - $cx; $dy = $tipY - $cy;
        $w = 4;
        $nx = -$dy * $w / $r; $ny = $dx * $w / $r;
        $b1 = [round($cx + $nx, 2), round($cy + $ny, 2)];
        $b2 = [round($cx - $nx, 2), round($cy - $ny, 2)];
    @endphp
    <div class="si-gauge-wrap">
        <svg class="si-gauge-svg" viewBox="0 0 220 128" aria-hidden="true">
            <defs>
                <linearGradient id="si-arc-fill" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="{{ $arcColor }}"/>
                    <stop offset="100%" stop-color="{{ $arcColor }}" stop-opacity="0.85"/>
                </linearGradient>
            </defs>
            <path class="si-gauge-arc-bg" d="M {{ $cx - $r }} {{ $cy }} A {{ $r }} {{ $r }} 0 0 1 {{ $cx + $r }} {{ $cy }}"/>
            <path class="si-gauge-arc-fill" d="M {{ $cx - $r }} {{ $cy }} A {{ $r }} {{ $r }} 0 0 1 {{ $cx + $r }} {{ $cy }}" stroke-dasharray="{{ $dashArray }}" stroke-dashoffset="0"/>
            @foreach($scalePositions as $p)
            <line class="si-gauge-tick" x1="{{ $p['t1'][0] }}" y1="{{ $p['t1'][1] }}" x2="{{ $p['t2'][0] }}" y2="{{ $p['t2'][1] }}"/>
            @endforeach
            @foreach($scalePositions as $p)
            <text x="{{ $p['x'] }}" y="{{ $p['y'] }}" class="si-gauge-scale">{{ $p['value'] }}</text>
            @endforeach
            <path class="si-gauge-needle" d="M {{ $b1[0] }} {{ $b1[1] }} L {{ $tipX }} {{ $tipY }} L {{ $b2[0] }} {{ $b2[1] }} Z"/>
            <circle class="si-gauge-dot" cx="{{ $tipX }}" cy="{{ $tipY }}" r="4" fill="{{ $dotColor }}" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
            <circle class="si-gauge-hub" cx="{{ $cx }}" cy="{{ $cy }}" r="6"/>
            <text x="{{ $cx }}" y="{{ $cy + 26 }}" class="si-gauge-value">{{ $scoreActuel }}</text>
            <text x="{{ $cx }}" y="{{ $cy + 38 }}" class="si-gauge-value-sub">sur {{ $scoreMax }} points</text>
        </svg>
        <p class="si-gauge-msg {{ $msgClass }}">{{ $msg }}</p>
    </div>

    {{-- Historique du score --}}
    <div class="si-block">
        <div class="si-block-title">Historique de ton score</div>
        <div class="si-block-body p-0">
            @if($historique->isEmpty())
            <div class="p-8 text-center">
                <p class="text-[#94a3b8] text-sm">Aucun historique pour le moment. Ton score est à {{ $scoreMax }}/{{ $scoreMax }}.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[#94a3b8] border-b border-white/10 bg-white/5">
                            <th class="py-3 px-4 font-medium">Heure de modification</th>
                            <th class="py-3 px-4 font-medium">Détails de l'infraction</th>
                            <th class="py-3 px-4 font-medium">Score modifié</th>
                            <th class="py-3 px-4 font-medium">Score conséquent</th>
                            <th class="py-3 px-4 font-medium">Sanctions d'infraction</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique as $h)
                        <tr class="border-b border-white/5 text-white/90 hover:bg-white/[0.03]">
                            <td class="py-3 px-4 whitespace-nowrap">{{ $h->heure_modification?->translatedFormat('d/m/Y H:i') ?? '—' }}</td>
                            <td class="py-3 px-4">{{ $h->details_infraction ?? '—' }}</td>
                            <td class="py-3 px-4 tabular-nums">{{ $h->score_avant }}</td>
                            <td class="py-3 px-4 tabular-nums font-medium">{{ $h->score_consequent }}</td>
                            <td class="py-3 px-4">{{ $h->sanction_infraction ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @elseif($createursAvecScore->isNotEmpty())
    {{-- Agent / Manageur / Directeur : liste des créateurs avec leur score --}}
    <div class="si-block">
        <div class="si-block-title">Scores d'intégrité par créateur</div>
        <div class="si-block-body p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[#94a3b8] border-b border-white/10 bg-white/5">
                            <th class="py-3 px-4 font-medium">Créateur</th>
                            <th class="py-3 px-4 font-medium">Score actuel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($createursAvecScore as $row)
                        <tr class="border-b border-white/5 text-white/90 hover:bg-white/[0.03]">
                            <td class="py-3 px-4 font-medium">{{ $row->createur->nom }}</td>
                            <td class="py-3 px-4 tabular-nums">{{ $row->score }}/{{ $scoreMax }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="si-block">
        <div class="si-block-body p-8 text-center">
            <p class="text-[#94a3b8] text-sm">Aucune fiche créateur associée à ton compte, ou aucun créateur dans ton périmètre.</p>
        </div>
    </div>
    @endif
</div>
@endsection
