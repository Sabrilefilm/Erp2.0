<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Planning des matchs – Unions Agency</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #0f172a; }
        .logo { font-size: 22px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px; }
        .subtitle { font-size: 13px; color: #475569; margin-top: 4px; }
        .period { font-size: 10px; color: #64748b; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #0f172a; color: #fff; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) { background: #f8fafc; }
        .footer { margin-top: 25px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Unions Agency</div>
        <div class="subtitle">Planning des matchs</div>
        <div class="period">Période : {{ \Carbon\Carbon::parse($from)->translatedFormat('d/m/Y') }} – {{ \Carbon\Carbon::parse($to)->translatedFormat('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Type</th>
                <th>Niveau</th>
                <th>Créateur</th>
                <th>Adversaire</th>
                <th>Équipe</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matchs as $m)
            <tr>
                <td>{{ $m->date ? \Carbon\Carbon::parse($m->date)->translatedFormat('d/m/Y') : '—' }}</td>
                <td>{{ $m->heure ? substr($m->heure, 0, 5) : '—' }}</td>
                <td>
                    @if($m->type === 'match_off')
                        {{ $m->avec_boost ? 'Match officiel avec boost' : 'Match officiel sans boost' }}
                    @else
                        {{ $typeLabels[$m->type] ?? $m->type }}
                    @endif
                </td>
                <td>{{ $m->type === 'match_off' && $m->niveau_match ? (\App\Models\Planning::NIVEAUX_MATCH_OFF[$m->niveau_match] ?? $m->niveau_match) : '—' }}</td>
                <td>{{ $m->createur?->nom ?? '—' }}</td>
                <td>{{ $m->createur_adverse_at ? (str_starts_with($m->createur_adverse_at, '@') ? $m->createur_adverse_at : '@'.$m->createur_adverse_at) : '—' }}</td>
                <td>{{ $m->createur && $m->createur->equipe ? $m->createur->equipe->nom : '—' }}</td>
                <td>{{ $statutLabels[$m->statut] ?? $m->statut }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #64748b;">Aucun match sur cette période.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Document généré le {{ now()->translatedFormat('d/m/Y à H:i') }} – Unions Agency
    </div>
</body>
</html>
