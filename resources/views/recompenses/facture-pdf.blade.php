<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $recompense->id }} – Unions Agency</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 28px; }
        .header { display: table; width: 100%; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #cbd5e1; }
        .header-left { display: table-cell; vertical-align: top; width: 50%; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; width: 50%; }
        .logo-img { max-height: 44px; max-width: 200px; margin-bottom: 6px; }
        .titre-facture { font-size: 22px; font-weight: bold; color: #0f172a; letter-spacing: 0.02em; margin: 0; }
        .header-right .ligne { font-size: 10px; color: #475569; margin: 2px 0; }
        .header-right .label { font-weight: bold; color: #0f172a; }
        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 6px; }
        .bloc-emetteur-dest { display: table; width: 100%; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #cbd5e1; }
        .bloc-emetteur { display: table-cell; width: 50%; vertical-align: top; padding-right: 20px; }
        .bloc-destinataire { display: table-cell; width: 50%; vertical-align: top; }
        .emetteur-ligne { font-size: 10px; color: #334155; margin: 2px 0; }
        .dest-ligne { font-size: 10px; color: #334155; margin: 2px 0; }
        .dest-nom { font-weight: bold; color: #0f172a; }
        table.documents { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.documents th { text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; color: #64748b; padding: 8px 10px; border-bottom: 2px solid #e2e8f0; }
        table.documents td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        table.documents .col-desc { width: 50%; }
        table.documents .col-prix { width: 18%; }
        table.documents .col-qte { width: 12%; }
        table.documents .col-total { width: 20%; text-align: right; font-weight: bold; }
        .reglement { margin-bottom: 20px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 10px; }
        .reglement-titre { font-weight: bold; color: #0f172a; margin-bottom: 6px; }
        .total-ttc { text-align: right; font-size: 14px; font-weight: bold; color: #0f172a; margin: 16px 0; }
        .footer { margin-top: 28px; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #64748b; }
        .footer p { margin: 6px 0; }
        .signature { margin-top: 20px; font-size: 10px; font-weight: bold; color: #0f172a; }
        .remerciement { margin-top: 14px; font-size: 10px; color: #475569; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if(!empty($logoDataUri))
                <img src="{{ $logoDataUri }}" alt="Unions Agency" class="logo-img" />
            @else
                <div class="titre-facture" style="font-size: 18px;">UNIONS AGENCY</div>
            @endif
            <p class="titre-facture" style="margin-top: 8px;">FACTURE</p>
        </div>
        <div class="header-right">
            <div class="ligne"><span class="label">DATE :</span> {{ $recompense->created_at->format('d / m / Y') }}</div>
            <div class="ligne"><span class="label">FACTURE N° :</span> {{ $recompense->id }}</div>
        </div>
    </div>

    <div class="bloc-emetteur-dest">
        <div class="bloc-emetteur">
            <div class="section-title">Émetteur</div>
            <div class="emetteur-ligne"><strong>{{ $facture['societe'] ?? 'Unions Agency' }}</strong></div>
            @if(!empty($facture['adresse']))
                <div class="emetteur-ligne">{{ $facture['adresse'] }}</div>
            @endif
            <div class="emetteur-ligne">Snapchat : {{ $facture['snapchat'] ?? 'unionsagency' }}</div>
            <div class="emetteur-ligne">{{ $facture['site'] ?? 'unionsagency.com' }}</div>
            <div class="emetteur-ligne">{{ $facture['forme'] ?? 'Société SARL' }}</div>
        </div>
        <div class="bloc-destinataire">
            <div class="section-title">Destinataire</div>
            <div class="dest-ligne dest-nom">{{ $recompense->createur?->nom ?? '—' }}</div>
            @if($recompense->createur?->email)
                <div class="dest-ligne">{{ $recompense->createur->email }}</div>
            @endif
        </div>
    </div>

    <div class="section-title">Documents</div>
    <table class="documents">
        <thead>
            <tr>
                <th class="col-desc">Description</th>
                <th class="col-prix">Prix unitaire</th>
                <th class="col-qte">Quantité</th>
                <th class="col-total">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $typeLabel = $typeLabels[$recompense->type] ?? $recompense->type;
                $montantAffiche = (float) $recompense->montant;
                $qteAffiche = 1;
                if (($recompense->type ?? '') === 'tiktok' || ($recompense->type ?? '') === 'TikTok') {
                    $montantAffiche = (float) $recompense->montant_tiktok;
                } elseif (($recompense->type ?? '') === 'carte_cadeau' && $recompense->montant_carte_cadeau && $recompense->quantite_carte_cadeau) {
                    $montantAffiche = (float) $recompense->montant_carte_cadeau;
                    $qteAffiche = (int) $recompense->quantite_carte_cadeau;
                }
                $totalLigne = $montantAffiche * $qteAffiche;
                $desc = 'Récompense';
                if ($recompense->raison) {
                    $desc .= ' – ' . $recompense->raison;
                }
                $desc .= ' (' . $typeLabel . ')';
                if (($recompense->type ?? '') === 'carte_cadeau' && $recompense->type_carte_cadeau) {
                    $desc .= ' – ' . (\App\Models\Recompense::TYPES_CARTE_CADEAU[$recompense->type_carte_cadeau] ?? $recompense->type_carte_cadeau);
                }
            @endphp
            <tr>
                <td>{{ $desc }}</td>
                <td>{{ number_format($montantAffiche, 2, ',', ' ') }} €</td>
                <td>{{ $qteAffiche }}</td>
                <td class="col-total">{{ number_format($totalLigne, 2, ',', ' ') }} €</td>
            </tr>
        </tbody>
    </table>

    @if(($recompense->type ?? '') === 'virement' && ($recompense->rib_iban || $recompense->rib_banque))
    <div class="reglement">
        <div class="reglement-titre">Règlement par virement bancaire</div>
        <div>Banque : {{ $recompense->rib_banque ?? '—' }}</div>
        <div>IBAN : {{ $recompense->rib_iban ?? '—' }}</div>
        <div>Titulaire : {{ $recompense->rib_prenom }} {{ $recompense->rib_nom }}</div>
        <div style="margin-top: 8px; font-size: 9px; color: #64748b;">La somme est créditée entre le 7 et le 12 du mois.</div>
    </div>
    @endif

    <div class="total-ttc">TOTAL TTC : {{ number_format($recompense->montant, 2, ',', ' ') }} €</div>

    @if(($recompense->type ?? '') === 'carte_cadeau')
    <div class="reglement" style="margin-top: 12px;">
        @if($recompense->montant_carte_cadeau && $recompense->quantite_carte_cadeau)
        <div style="margin-bottom: 8px; font-size: 10px;">{{ $recompense->quantite_carte_cadeau }} carte(s) × {{ number_format($recompense->montant_carte_cadeau, 0, ',', ' ') }} €</div>
        @endif
        <div class="reglement-titre">Code carte cadeau</div>
        @if($recompense->code_cadeau)
            <div style="margin-top: 8px; padding: 14px 18px; background: #fef3c7; border: 2px solid #d97706; border-radius: 10px;">
                <div style="font-family: monospace; font-size: 18px; font-weight: bold; letter-spacing: 0.2em; color: #92400e; word-break: break-all;">{{ $recompense->code_cadeau }}</div>
                <div style="font-size: 9px; color: #78716c; margin-top: 6px;">Utilisez ce code sur le site partenaire. Valable une fois, expire 1 an après la date d’achat.</div>
            </div>
        @else
            <div style="color: #64748b;">Code non encore renseigné.</div>
        @endif
        <div style="margin-top: 8px; font-size: 9px; color: #64748b;">Ce code est valable une seule fois dès qu'il est utilisé. Il expire 1 an à compter de la date d'achat.</div>
    </div>
    @endif

    <div class="footer">
        <p>En cas de retard de paiement, et conformément au code de commerce, une indemnité calculée à trois fois le taux d'intérêt légal ainsi qu'un frais de recouvrement de 40 euros sont exigibles.</p>
        <p>Conditions générales consultables sur le site : {{ $facture['site'] ?? 'unionsagency.com' }}</p>
        <p class="signature">Signature de Unions Agency</p>
        <p class="remerciement">Les fondateurs Unions Agency vous remercient pour votre engagement en espérant vous garder auprès de nous.</p>
    </div>
</body>
</html>
