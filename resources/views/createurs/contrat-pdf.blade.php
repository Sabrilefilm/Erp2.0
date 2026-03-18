<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contrat de prestation – {{ $createur->nom ?? 'Créateur' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 20px; line-height: 1.4; }
        .titre { font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 16px; }
        .sous-titre { font-size: 11px; text-align: center; margin-bottom: 20px; color: #475569; }
        .partie { margin-bottom: 14px; }
        .partie strong { display: block; margin-bottom: 4px; font-size: 10px; }
        .article { margin-bottom: 12px; }
        .article-titre { font-weight: bold; margin-bottom: 4px; }
        .article ul { margin: 4px 0 0 18px; padding: 0; }
        .article li { margin-bottom: 2px; }
        .signature-block { margin-top: 24px; }
        .signature-line { border-bottom: 1px solid #1e293b; min-height: 18px; margin-top: 4px; display: inline-block; min-width: 200px; }
        .signature-label { font-size: 9px; color: #64748b; margin-top: 2px; }
    </style>
</head>
<body>
    <div class="titre">CONTRAT OFFICIEL DE PRESTATION – UNIONS AGENCY</div>
    <div class="sous-titre">Entre :</div>

    <p><strong>UNIONS AGENCY</strong>, agence spécialisée dans l'accompagnement de créateurs TikTok Live,<br>
    Représentée par : <span class="signature-line"></span><br>
    Ci-après dénommée « L'Agence »</p>

    <p><strong>ET</strong></p>

    <p>
        Nom / Prénom : <strong>{{ $createur->nom ?? '____________________________' }}</strong><br>
        Nom TikTok : <strong>{{ $nomTiktok }}</strong><br>
        Email : <strong>{{ $email }}</strong><br>
        Téléphone : <strong>{{ $telephone }}</strong><br>
        Ci-après dénommé(e) « Le Créateur »
    </p>

    <div class="article">
        <div class="article-titre">ARTICLE 1 – OBJET</div>
        <p>Le présent contrat encadre la collaboration entre L'Agence et le Créateur pour le développement de son activité TikTok Live.</p>
    </div>

    <div class="article">
        <div class="article-titre">ARTICLE 2 – ENGAGEMENTS DU CRÉATEUR</div>
        <ul>
            <li>Effectuer minimum 7 jours de live par mois.</li>
            <li>Réaliser minimum 16 heures de live par mois.</li>
            <li>Posséder un seul compte TikTok affilié à l'agence.</li>
            <li>Respecter la stratégie live et contenu définie par l'agence.</li>
            <li>Respecter le règlement intérieur officiel de l'agence.</li>
            <li>Respecter strictement les règles TikTok.</li>
        </ul>
    </div>

    <div class="article">
        <div class="article-titre">ARTICLE 3 – RÉMUNÉRATION</div>
        <p>L'Agence percevra une commission de ______ % sur les revenus générés via TikTok (diamants, cadeaux, partenariats négociés).</p>
    </div>

    <div class="article">
        <div class="article-titre">ARTICLE 4 – DURÉE ET PRÉAVIS</div>
        <p>Le contrat est conclu pour une durée indéterminée avec un préavis obligatoire de 90 jours en cas de résiliation par le Créateur.</p>
        <p>Conformément au droit de rétractation légal, le Créateur dispose d'un délai de 15 jours à compter de la signature pour quitter l'agence sans préavis.</p>
    </div>

    <div class="article">
        <div class="article-titre">ARTICLE 5 – RÉSILIATION IMMÉDIATE</div>
        <p>L'Agence peut résilier le contrat à tout moment en cas de non-respect des consignes, du règlement intérieur ou des règles TikTok.</p>
        <p>En cas de bannissement, suspension ou décision de TikTok impactant la collaboration, le contrat pourra être résilié immédiatement.</p>
    </div>

    <div class="article">
        <div class="article-titre">ARTICLE 6 – EXCLUSIVITÉ</div>
        <p>Le Créateur s'engage à ne pas collaborer avec une autre agence TikTok pendant la durée du contrat sans accord écrit de L'Agence.</p>
    </div>

    <div class="article">
        <div class="article-titre">ARTICLE 7 – SANCTIONS</div>
        <p>En cas de manquement : avertissement, perte de récompenses, suspension interne ou exclusion définitive pourront être appliqués.</p>
    </div>

    <p style="margin-top: 20px;">Fait à ____________________, le {{ $dateSignature }}</p>

    <div class="signature-block">
        <div>Signature de L'Agence : <span class="signature-line"></span></div>
        <div style="margin-top: 16px;">
            Signature du Créateur :
            @if(!empty($signedAt))
            <strong>Signé électroniquement par {{ $createur->nom ?? 'Le Créateur' }} le {{ $signedAt }}</strong>
            @else
            <span class="signature-line"></span>
            @endif
        </div>
    </div>
</body>
</html>
