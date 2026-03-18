@extends('layouts.app')

@section('title', 'Aide & informations')

@push('styles')
<style>
.aide-page { max-width: 56rem; margin: 0 auto; padding-bottom: 3rem; }
.aide-hero {
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(6,182,212,0.15) 0%, rgba(139,92,246,0.1) 50%, rgba(34,197,94,0.08) 100%);
    border: 1px solid rgba(6,182,212,0.25);
    padding: 24px 28px 28px;
    position: relative;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}
.aide-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #06b6d4, #8b5cf6, #22c55e);
    border-radius: 20px 20px 0 0;
    opacity: 0.9;
}
.aide-hero-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: rgba(6,182,212,0.25);
    border: 1px solid rgba(6,182,212,0.35);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.aide-hero-title { font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
.aide-hero-sub { font-size: 0.875rem; color: #94a3b8; margin-top: 6px; }
.aide-section-title {
    font-size: 11px;
    font-weight: 700;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 12px;
}
.aide-nav-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.aide-nav-card {
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    padding: 18px 20px;
    text-decoration: none;
    color: inherit;
    display: block;
    transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
}
.aide-nav-card:hover { border-color: rgba(6,182,212,0.3); transform: translateY(-2px); box-shadow: 0 12px 28px rgba(0,0,0,0.25); text-decoration: none; color: inherit; }
.aide-nav-card .icon-wrap { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 10px; }
.aide-nav-card.pc .icon-wrap { background: rgba(6,182,212,0.2); }
.aide-nav-card.mobile .icon-wrap { background: rgba(139,92,246,0.2); }
.aide-nav-card h3 { font-size: 1rem; font-weight: 700; color: #fff; margin: 0 0 4px 0; }
.aide-nav-card p { font-size: 0.8125rem; color: #94a3b8; margin: 0; line-height: 1.4; }
.aide-modules { display: grid; grid-cols: 1; gap: 10px; }
@media (min-width: 640px) { .aide-modules { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .aide-modules { grid-template-columns: repeat(3, 1fr); } }
.aide-module-link {
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    padding: 14px 16px;
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: border-color 0.25s, background 0.25s, transform 0.2s;
    position: relative;
    overflow: hidden;
}
.aide-module-link::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: var(--c, #06b6d4);
    opacity: 0.85;
    border-radius: 14px 14px 0 0;
}
.aide-module-link:hover { border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.06); transform: translateY(-1px); text-decoration: none; color: inherit; }
.aide-module-link .icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
.aide-module-link .text { min-width: 0; }
.aide-module-link .text strong { font-size: 0.9375rem; font-weight: 600; color: #fff; display: block; margin-bottom: 2px; }
.aide-module-link .text span { font-size: 0.75rem; color: #94a3b8; line-height: 1.35; }
.aide-module-link .arrow { margin-left: auto; flex-shrink: 0; color: rgba(255,255,255,0.3); transition: transform 0.2s; }
.aide-module-link:hover .arrow { transform: translateX(4px); color: #06b6d4; }
.aide-cta { text-align: center; margin-top: 2rem; }
.aide-cta a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 14px;
    background: rgba(6,182,212,0.2);
    border: 1px solid rgba(6,182,212,0.35);
    color: #67e8f9;
    font-weight: 600;
    font-size: 0.9375rem;
    text-decoration: none;
    transition: background 0.25s, border-color 0.25s, color 0.25s;
}
.aide-cta a:hover { background: rgba(6,182,212,0.3); border-color: rgba(6,182,212,0.5); color: #fff; text-decoration: none; }
</style>
@endpush

@section('content')
<div class="aide-page space-y-8">
    {{-- Hero ── --}}
    <div class="aide-hero">
        <div class="flex items-center gap-4">
            <div class="aide-hero-icon" aria-hidden="true">💡</div>
            <div>
                <h1 class="aide-hero-title">Aide & informations</h1>
                <p class="aide-hero-sub">Tout ce dont vous avez besoin : navigation, modules et accès rapides en un seul endroit.</p>
            </div>
        </div>
    </div>

    {{-- Navigation (PC / Mobile) ── --}}
    <section>
        <h2 class="aide-section-title">Navigation</h2>
        <div class="aide-nav-cards">
            <div class="aide-nav-card pc">
                <div class="icon-wrap">💻</div>
                <h3>Sur ordinateur</h3>
                <p>Le menu à gauche regroupe toutes les sections. La recherche en haut permet de naviguer rapidement. Profil et déconnexion en bas du menu ou dans le menu en haut à droite.</p>
            </div>
            <div class="aide-nav-card mobile">
                <div class="icon-wrap">📱</div>
                <h3>Sur mobile</h3>
                <p>Barre en bas : Accueil, Ma fiche / Utilisateurs, et le bouton « Menu » pour ouvrir le panneau avec toutes les sections. Même app, adaptée au tactile.</p>
            </div>
        </div>
    </section>

    {{-- Tous les modules (liens directs) ── --}}
    <section>
        <h2 class="aide-section-title">Tous les modules — accès rapide</h2>
        <div class="aide-modules">
            <a href="{{ route('dashboard') }}" class="aide-module-link" style="--c: #0ea5e9;">
                <span class="icon" style="background: rgba(14,165,233,0.2);">🏠</span>
                <div class="text"><strong>Vue d'ensemble</strong><span>Tableau de bord, résumé et accès rapides</span></div>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <a href="{{ route('matches.index') }}" class="aide-module-link" style="--c: #3b82f6;">
                <span class="icon" style="background: rgba(59,130,246,0.2);">📅</span>
                <div class="text"><strong>Matchs</strong><span>Programmer ou demander un match, planning</span></div>
                <span class="arrow">→</span>
            </a>
            <a href="{{ route('recompenses.index') }}" class="aide-module-link" style="--c: #22c55e;">
                <span class="icon" style="background: rgba(34,197,94,0.2);">🎁</span>
                <div class="text"><strong>Récompenses</strong><span>Consulter et gérer les récompenses (virement, TikTok, carte cadeau)</span></div>
                <span class="arrow">→</span>
            </a>
            <a href="{{ route('regles.index') }}" class="aide-module-link" style="--c: #8b5cf6;">
                <span class="icon" style="background: rgba(139,92,246,0.2);">💬</span>
                <div class="text"><strong>Le Message de l'agence</strong><span>Consulter et gérer les règles de l'agence</span></div>
                <span class="arrow">→</span>
            </a>
            <a href="{{ route('formations.index') }}" class="aide-module-link" style="--c: #14b8a6;">
                <span class="icon" style="background: rgba(20,184,166,0.2);">📚</span>
                <div class="text"><strong>Formations</strong><span>Cours, vidéos, documents et ressources</span></div>
                <span class="arrow">→</span>
            </a>
            @if(auth()->user()->isCreateur())
            <a href="{{ route('createurs.index') }}" class="aide-module-link" style="--c: #06b6d4;">
                <span class="icon" style="background: rgba(6,182,212,0.2);">👤</span>
                <div class="text"><strong>Ma fiche</strong><span>Voir et modifier ma fiche créateur</span></div>
                <span class="arrow">→</span>
            </a>
            @else
            <a href="{{ route('users.index') }}" class="aide-module-link" style="--c: #06b6d4;">
                <span class="icon" style="background: rgba(6,182,212,0.2);">👤</span>
                <div class="text"><strong>Utilisateurs</strong><span>Gérer les comptes et les fiches</span></div>
                <span class="arrow">→</span>
            </a>
            @endif
            @if(auth()->user()->canSeeScoreIntegrite())
            <a href="{{ route('score-integrite.index') }}" class="aide-module-link" style="--c: #0ea5e9;">
                <span class="icon" style="background: rgba(14,165,233,0.2);">📊</span>
                <div class="text"><strong>Score d'intégrité</strong><span>Score de conformité (0-100), historique et sanctions</span></div>
                <span class="arrow">→</span>
            </a>
            @endif
            <a href="{{ route('score-fidelite.index') }}" class="aide-module-link" style="--c: #22c55e;">
                <span class="icon" style="background: rgba(34,197,94,0.2);">🎯</span>
                <div class="text"><strong>Bonnes actions</strong><span>Score du mois, points et récompenses (80 points / 100 points)</span></div>
                <span class="arrow">→</span>
            </a>
            @if(!auth()->user()->isFondateur() && !auth()->user()->isCreateur())
            <a href="{{ route('rapport-vendredi.index') }}" class="aide-module-link" style="--c: #f59e0b;">
                <span class="icon" style="background: rgba(245,158,11,0.2);">📝</span>
                <div class="text"><strong>Rapport de la semaine</strong><span>Remplir le rapport hebdomadaire</span></div>
                <span class="arrow">→</span>
            </a>
            @endif
            <a href="{{ route('documents-officiels.index') }}" class="aide-module-link" style="--c: #64748b;">
                <span class="icon" style="background: rgba(100,116,139,0.25);">📄</span>
                <div class="text"><strong>Contrat et Règlement</strong><span>Documents officiels, signature et informations</span></div>
                <span class="arrow">→</span>
            </a>
            @if(auth()->user()->hasRoleOrAbove('agent') || auth()->user()->isCreateur())
            <a href="{{ route('messagerie.index') }}" class="aide-module-link" style="--c: #1877f2;">
                <span class="icon" style="background: rgba(24,119,242,0.2);">✉️</span>
                <div class="text"><strong>Messagerie</strong><span>Échanger avec l'agence et les membres</span></div>
                <span class="arrow">→</span>
            </a>
            @endif
            <a href="{{ route('notifications.index') }}" class="aide-module-link" style="--c: #a855f7;">
                <span class="icon" style="background: rgba(168,85,247,0.2);">🔔</span>
                <div class="text"><strong>Notifications</strong><span>Voir toutes les notifications</span></div>
                <span class="arrow">→</span>
            </a>
            @if(!auth()->user()->isCreateur() && !auth()->user()->isFondateur())
            <a href="{{ route('blacklist.index') }}" class="aide-module-link" style="--c: #64748b;">
                <span class="icon" style="background: rgba(100,116,139,0.2);">🚫</span>
                <div class="text"><strong>Liste noire</strong><span>Gestion de la liste noire</span></div>
                <span class="arrow">→</span>
            </a>
            @endif
            @if(auth()->user()->isFondateurPrincipal())
            <a href="{{ route('import.index') }}" class="aide-module-link" style="--c: #0d9488;">
                <span class="icon" style="background: rgba(13,148,136,0.2);">📥</span>
                <div class="text"><strong>Import</strong><span>Importer des données (créateurs, etc.)</span></div>
                <span class="arrow">→</span>
            </a>
            <a href="{{ route('donnees-match.index') }}" class="aide-module-link" style="--c: #6366f1;">
                <span class="icon" style="background: rgba(99,102,241,0.2);">📋</span>
                <div class="text"><strong>Données match</strong><span>Gérer les données des matchs</span></div>
                <span class="arrow">→</span>
            </a>
            <a href="{{ route('equipes.index') }}" class="aide-module-link" style="--c: #8b5cf6;">
                <span class="icon" style="background: rgba(139,92,246,0.2);">🏢</span>
                <div class="text"><strong>Agences & extérieur</strong><span>Liste des agences et attribution</span></div>
                <span class="arrow">→</span>
            </a>
            <a href="{{ route('diagnostic.index') }}" class="aide-module-link" style="--c: #ec4899;">
                <span class="icon" style="background: rgba(236,72,153,0.2);">📈</span>
                <div class="text"><strong>Diagnostic</strong><span>Vue diagnostic et indicateurs</span></div>
                <span class="arrow">→</span>
            </a>
            @endif
        </div>
    </section>

    <div class="aide-cta">
        <a href="{{ route('dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Retour à l'accueil
        </a>
    </div>
</div>
@endsection
