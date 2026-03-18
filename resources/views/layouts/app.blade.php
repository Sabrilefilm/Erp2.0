<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ultra')</title>
    @include('partials.head-assets')
    @stack('styles')
    {{-- OneSignal (notifications push) --}}
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    {{-- Sidebar desktop + Menu mobile : fond sombre, Déconnexion visible --}}
    @verbatim
    <style>
    /* Desktop : sidebar */
    @media (min-width: 768px) {
        #app-sidebar.app-sidebar {
            background: linear-gradient(180deg, #060a10 0%, #0e1219 50%, #060a10 100%) !important;
            border-right: 1px solid rgba(255,255,255,0.12) !important;
        }
        #app-sidebar .app-sidebar-logo a { color: #fff !important; }
        #app-sidebar .app-sidebar-nav-item { color: #94a3b8 !important; }
        #app-sidebar .app-sidebar-nav-item:hover,
        #app-sidebar .app-sidebar-nav-item.is-active { color: #fff !important; }
        #app-sidebar .app-sidebar-footer {
            background: rgba(15,23,42,0.98) !important;
            border-top: 1px solid rgba(255,255,255,0.12) !important;
            margin-top: auto !important;
        }
        #app-sidebar .app-sidebar-user-name { color: #fff !important; }
        #app-sidebar .app-sidebar-user-role { color: #94a3b8 !important; }
        #app-sidebar .app-sidebar-logout {
            display: block !important;
            width: 100% !important;
            padding: 0.625rem 0.75rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid rgba(248,113,113,0.5) !important;
            background: rgba(244,63,94,0.2) !important;
            color: #fda4af !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            text-align: center !important;
            cursor: pointer !important;
        }
        #app-sidebar .app-sidebar-logout:hover {
            background: rgba(244,63,94,0.35) !important;
            color: #fff !important;
        }
    }
    /* Mobile : tiroir Menu (fond sombre, textes en blanc, Déconnexion visible) */
    @media (max-width: 767px) {
        #menu-bottom-sheet .ultra-menu-panel.menu-drawer-modern {
            background: linear-gradient(180deg, #060a10 0%, #0e1219 40%, #060a10 100%) !important;
            border-top: 1px solid rgba(255,255,255,0.12) !important;
            color: #fff !important;
        }
        #menu-bottom-sheet .menu-drawer-header span {
            color: #fff !important;
        }
        #menu-bottom-sheet .menu-drawer-profile p,
        #menu-bottom-sheet .menu-drawer-profile .font-bold {
            color: #fff !important;
        }
        #menu-bottom-sheet .menu-drawer-profile .text-\[#94a3b8\] {
            color: rgba(255,255,255,0.85) !important;
        }
        #menu-bottom-sheet .menu-drawer-link {
            color: #fff !important;
        }
        #menu-bottom-sheet .menu-drawer-link .menu-drawer-icon,
        #menu-bottom-sheet .menu-drawer-link .menu-drawer-icon svg {
            color: #fff !important;
        }
        #menu-bottom-sheet .menu-drawer-link.is-active {
            color: #7dd3fc !important;
        }
        #menu-bottom-sheet .menu-drawer-link.is-active .menu-drawer-icon {
            color: #7dd3fc !important;
        }
        #menu-bottom-sheet .menu-drawer-signout {
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
            margin-top: auto !important;
            padding: 1rem 1rem max(1rem, env(safe-area-inset-bottom)) !important;
            background: rgba(15,23,42,0.98) !important;
            border-top: 1px solid rgba(255,255,255,0.15) !important;
        }
        #menu-bottom-sheet .menu-drawer-logout-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.5rem !important;
            width: 100% !important;
            padding: 0.875rem 1rem !important;
            border-radius: 0.75rem !important;
            border: none !important;
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%) !important;
            color: #fff !important;
            font-size: 0.9375rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            box-shadow: 0 4px 14px rgba(244,63,94,0.4) !important;
        }
        #menu-bottom-sheet .menu-drawer-logout-btn:hover {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;
            color: #fff !important;
        }
        /* Lien Déconnexion dans la liste (visible au scroll) */
        #menu-bottom-sheet .menu-drawer-signout-link {
            color: #fda4af !important;
        }
        #menu-bottom-sheet .menu-drawer-signout-link:hover {
            color: #fff !important;
            background: rgba(244,63,94,0.2) !important;
        }
        #menu-bottom-sheet .menu-drawer-signout-link .menu-drawer-icon,
        #menu-bottom-sheet .menu-drawer-signout-link .menu-drawer-icon svg {
            color: #fda4af !important;
        }
        #menu-bottom-sheet .menu-drawer-signout-link:hover .menu-drawer-icon {
            color: #fff !important;
        }
        /* Panneaux Notifications / Messagerie mobile : pleine largeur, texte lisible */
        .ultra-top-bar-mobile {
            overflow: visible !important;
        }
        .ultra-notif-mobile,
        .ultra-msg-mobile {
            box-sizing: border-box;
            left: 0.75rem !important;
            right: 0.75rem !important;
            width: auto !important;
            max-width: none !important;
        }
        .ultra-notif-mobile a[href*="notifications"],
        .ultra-msg-mobile a[href*="messagerie"] {
            box-sizing: border-box;
            min-width: 0;
        }
        .ultra-notif-mobile .min-w-0 p,
        .ultra-msg-mobile .min-w-0 p {
            max-width: 100%;
            overflow-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
        }
        .ultra-notif-mobile .px-4,
        .ultra-msg-mobile .px-3,
        .ultra-msg-mobile .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    /* Bouton Webleb — tous les effets (P-Match / Créer utilisateur) — plus petit */
    .btn-webleb-wrap { display: flex; justify-content: flex-end; flex-shrink: 0; align-items: center; }
    .btn-webleb {
        display: flex; align-items: center; padding: 5px 14px; text-decoration: none;
        font-family: 'Poppins', sans-serif; font-size: 0.8125rem; font-weight: 900; font-style: italic;
        color: white; background: #6225E6; cursor: pointer; user-select: none;
        transition: box-shadow 1s ease, transform 0.2s ease;
        box-shadow: 4px 4px 0 black; transform: skewX(-15deg);
    }
    .btn-webleb:focus { outline: none; }
    .btn-webleb:hover { transition: box-shadow 0.5s ease; box-shadow: 7px 7px 0 #FBC638; }
    .btn-webleb:active { transform: skewX(-15deg) scale(0.98); }
    .btn-webleb span { transform: skewX(15deg); display: inline-flex; align-items: center; }
    .btn-webleb span:nth-child(2) {
        transition: margin-right 0.5s ease; margin-right: 0; width: 14px; margin-left: 16px;
        position: relative; top: 10%; display: inline-flex; align-items: center; justify-content: center;
    }
    .btn-webleb:hover span:nth-child(2) { transition: margin-right 0.5s ease; margin-right: 28px; }
    .btn-webleb span:nth-child(2) svg { width: 20px; height: auto; max-height: 12px; display: block; }
    .btn-webleb path.one { transition: transform 0.4s ease, fill 0.3s ease; transform: translateX(-60%); fill: #FFFFFF; }
    .btn-webleb path.two { transition: transform 0.5s ease, fill 0.3s ease; transform: translateX(-30%); fill: #FFFFFF; }
    .btn-webleb path.three { transition: fill 0.3s ease; fill: #FFFFFF; }
    .btn-webleb:hover path.three { animation: webleb_color_anim 1s ease-in-out infinite 0.2s; }
    .btn-webleb:hover path.one { transform: translateX(0%); animation: webleb_color_anim 1s ease-in-out infinite 0.6s; }
    .btn-webleb:hover path.two { transform: translateX(0%); animation: webleb_color_anim 1s ease-in-out infinite 0.4s; }
    @keyframes webleb_color_anim { 0% { fill: white; } 50% { fill: #FBC638; } 100% { fill: white; } }
    @-webkit-keyframes webleb_color_anim { 0% { fill: white; } 50% { fill: #FBC638; } 100% { fill: white; } }

    /* Zone principale : fond moderne (gradient discret + entrée en douceur) */
    .ultra-main-modern {
      position: relative;
      animation: ultraMainFade 0.5s ease-out;
    }
    .ultra-main-modern::before {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      z-index: -1;
      border-radius: 0;
      background:
        radial-gradient(ellipse 80% 50% at 70% 0%, rgba(0, 212, 255, 0.07) 0%, transparent 55%),
        radial-gradient(ellipse 60% 40% at 20% 100%, rgba(167, 139, 250, 0.06) 0%, transparent 50%);
    }
    @keyframes ultraMainFade {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Sous-menus déroulants (sidebar PC + tiroir mobile) */
    .nav-dropdown { margin-bottom: 0.25rem; }
    .nav-dropdown-panel {
      overflow: hidden;
      max-height: 0;
      opacity: 0;
      transition: max-height 0.25s ease, opacity 0.2s ease;
    }
    .nav-dropdown.is-open .nav-dropdown-panel {
      max-height: 500px;
      opacity: 1;
    }
    .nav-dropdown .nav-dropdown-chevron { display: inline-flex; align-items: center; justify-content: center; }
    .nav-dropdown .nav-dropdown-chevron svg { width: 100%; height: 100%; }
    .nav-dropdown.is-open .nav-dropdown-chevron { transform: rotate(180deg); }
    #app-sidebar .nav-dropdown-trigger:hover { background: rgba(255, 255, 255, 0.06); color: #fff; }
    #menu-bottom-sheet .menu-drawer-dropdown-trigger { color: #94a3b8 !important; }
    #menu-bottom-sheet .menu-drawer-dropdown-trigger:hover { color: #fff !important; }
    #menu-bottom-sheet .nav-dropdown-chevron { color: inherit; }

    /* Fond : dégradé bleu / or + vagues mer */
    .app-bg-animation {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
        background: linear-gradient(180deg,
            #060a10 0%,
            #0d1520 35%,
            #0e1219 70%,
            #060a10 100%);
        background-size: 200% 200%;
        animation: app-bg-move 12s ease-in-out infinite;
    }
    @keyframes app-bg-move {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .app-bg-waves {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 35%;
        min-height: 180px;
    }
    .app-bg-wave {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 200%;
        height: 100%;
        fill: none;
        stroke: none;
    }
    .app-bg-wave--1 {
        fill: rgba(8, 12, 18, 0.5);
        animation: app-wave 9s ease-in-out infinite;
    }
    .app-bg-wave--2 {
        fill: rgba(14, 18, 25, 0.45);
        animation: app-wave 11s ease-in-out infinite 0.5s;
    }
    .app-bg-wave--3 {
        fill: rgba(13, 21, 32, 0.4);
        animation: app-wave 8s ease-in-out infinite 1s;
    }
    @keyframes app-wave {
        0%, 100% { transform: translateX(0) translateY(0); }
        25% { transform: translateX(-6%) translateY(2%); }
        50% { transform: translateX(-25%) translateY(0); }
        75% { transform: translateX(-19%) translateY(-2%); }
    }
    </style>
    @endverbatim
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@1,900&display=swap" rel="stylesheet">
</head>
<body class="ultra-premium min-h-screen text-white relative">
@php
    /* Compteur de messages non lus — disponible dans tout le layout */
    $unreadMessagesCount = auth()->check()
        ? \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count()
        : 0;
    /* Dernières notifications pour la mini-fenêtre (dropdown) */
    $headerNotifications = auth()->check()
        ? auth()->user()->notifications()->orderByDesc('created_at')->limit(8)->get()
        : collect();
    $unreadNotificationsCount = auth()->check()
        ? auth()->user()->unreadNotifications()->count()
        : 0;
    /* Dernières conversations pour le dropdown Messagerie (même style que notifications) */
    $headerConversations = collect();
    if (auth()->check()) {
        $me = auth()->user();
        $lastMsgs = \App\Models\Message::with(['sender', 'receiver'])
            ->where('sender_id', $me->id)
            ->orWhere('receiver_id', $me->id)
            ->orderByDesc('created_at')
            ->get();
        $byOther = $lastMsgs->groupBy(fn ($m) => $m->sender_id === $me->id ? $m->receiver_id : $m->sender_id)
            ->map(fn ($msgs) => $msgs->first())
            ->take(8);
        $headerConversations = $byOther->values();
    }
@endphp
    {{-- Fond : dégradé bleu / or + vagues type mer --}}
    <div class="app-bg-animation" aria-hidden="true">
        <div class="app-bg-waves">
            <svg class="app-bg-wave app-bg-wave--1" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,60 C300,120 600,0 900,60 C1050,90 1200,30 1200,60 L1200,120 L0,120 Z"></path>
            </svg>
            <svg class="app-bg-wave app-bg-wave--2" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,80 C250,20 550,100 900,50 C1050,20 1200,70 1200,80 L1200,120 L0,120 Z"></path>
            </svg>
            <svg class="app-bg-wave app-bg-wave--3" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,50 C400,110 800,10 1200,50 L1200,120 L0,120 Z"></path>
            </svg>
        </div>
    </div>
    <div class="relative z-10 flex flex-col min-h-screen">

        <aside class="app-sidebar" id="app-sidebar" aria-label="Menu principal">
            <div class="app-sidebar-logo">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline text-white font-bold" aria-label="Ultra - Accueil">
                    <span class="ultra-top-bar-brand">Ultra</span>
                    <span class="app-sidebar-logo-icon w-8 h-8 rounded-full bg-[#1877f2] flex items-center justify-center text-sm flex-shrink-0">U</span>
                </a>
            </div>
            <nav class="app-sidebar-nav">
                <a href="{{ route('dashboard') }}" class="app-sidebar-nav-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></span>
                    <span>Vue d'ensemble</span>
                </a>

                @if(auth()->user()->isFondateur())
                {{-- Sous-menu déroulant Administration (PC) --}}
                <div class="app-sidebar-nav-group nav-dropdown {{ (request()->routeIs('users.*') || request()->routeIs('password.generate-code*') || request()->routeIs('blacklist.*') || request()->routeIs('score-integrite.gestion') || request()->routeIs('import.*') || request()->routeIs('rapport-vendredi.*') || request()->routeIs('donnees-match.*')) ? 'is-open' : '' }}" data-nav-dropdown>
                    <button type="button" class="app-sidebar-nav-item nav-dropdown-trigger w-full text-left flex items-center justify-between gap-2 cursor-pointer border-0 bg-transparent" aria-expanded="{{ (request()->routeIs('users.*') || request()->routeIs('import.*') || request()->routeIs('rapport-vendredi.*')) ? 'true' : 'false' }}" aria-controls="sidebar-dropdown-admin" id="sidebar-trigger-admin">
                        <span class="flex items-center gap-3">
                            <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                            <span>Administration</span>
                        </span>
                        <span class="nav-dropdown-chevron w-4 h-4 flex-shrink-0 transition-transform duration-200" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></span>
                    </button>
                    <div class="nav-dropdown-panel" id="sidebar-dropdown-admin" role="region" aria-labelledby="sidebar-trigger-admin">
                        <a href="{{ route('users.index') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('users.*') ? 'is-active' : '' }}">
                            <span>Utilisateurs</span>
                        </a>
                        <a href="{{ route('password.generate-code.form') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('password.generate-code*') ? 'is-active' : '' }}">
                            <span>Générer un code mot de passe</span>
                        </a>
                        <a href="{{ route('blacklist.index') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('blacklist.*') ? 'is-active' : '' }}">
                            <span>Liste noire</span>
                        </a>
                        @if(auth()->user()->isFondateurPrincipal())
                        <a href="{{ route('score-integrite.gestion') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('score-integrite.gestion') ? 'is-active' : '' }}">
                            <span>Infractions (score)</span>
                        </a>
                        <a href="{{ route('import.index') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('import.index') || request()->routeIs('import.store') || request()->routeIs('import.template') ? 'is-active' : '' }}">
                            <span>Import Excel</span>
                        </a>
                        <a href="{{ route('import.corriger-heures-jours') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('import.corriger-heures-jours') || request()->routeIs('import.mettre-a-jour-heures-jours') ? 'is-active' : '' }}">
                            <span>Corriger heures et jours</span>
                        </a>
                        <a href="{{ route('rapport-vendredi.index') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('rapport-vendredi.*') ? 'is-active' : '' }}">
                            <span>Rapport de la semaine</span>
                        </a>
                        <a href="{{ route('donnees-match.index') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('donnees-match.*') ? 'is-active' : '' }}">
                            <span>Données match (répertoire)</span>
                        </a>
                        @endif
                    </div>
                </div>
                @elseif(!auth()->user()->isCreateur() && !auth()->user()->isAgent())
                <a href="{{ route('users.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('users.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                    <span>Utilisateurs</span>
                </a>
                @if(auth()->user()->hasRoleOrAbove('manageur'))
                <a href="{{ route('password.generate-code.form') }}" class="app-sidebar-nav-item {{ request()->routeIs('password.generate-code*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></span>
                    <span>Générer un code mot de passe</span>
                </a>
                @endif
                @endif
                {{-- Matchs, Récompenses, Message, Formations : visible pour tous --}}
                <a href="{{ route('matches.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('matches.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                    <span>Matchs</span>
                </a>
                @if(auth()->user()->isAgent())
                <a href="{{ route('createurs.mes-createurs') }}" class="app-sidebar-nav-item {{ request()->routeIs('createurs.mes-createurs') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                    <span>Mes créateurs</span>
                </a>
                @endif
                <a href="{{ route('recompenses.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('recompenses.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg></span>
                    <span>Récompenses</span>
                </a>
                <a href="{{ route('annonces.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('annonces.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></span>
                    <span>Annonces & Campagnes</span>
                </a>
                <a href="{{ route('formations.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('formations.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></span>
                    <span>Nos Formations</span>
                </a>
                @if(!auth()->user()->isCreateur() && !auth()->user()->isFondateur())
                <a href="{{ route('blacklist.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('blacklist.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></span>
                    <span>Liste noire</span>
                </a>
                @endif
                @if(auth()->user()->canSeeScoreIntegrite())
                <a href="{{ route('score-integrite.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('score-integrite.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    <span>Score d'intégrité</span>
                </a>
                @endif
                {{-- Bonnes actions (score fidélité) : visible pour tous --}}
                <a href="{{ route('score-fidelite.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('score-fidelite.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg></span>
                    <span>Bonnes actions</span>
                </a>
                @if(!auth()->user()->isFondateur() && !auth()->user()->isCreateur())
                <a href="{{ route('rapport-vendredi.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('rapport-vendredi.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
                    <span>Rapport de la semaine</span>
                </a>
                @endif
                @if(auth()->user()->isFondateurPrincipal())
                {{-- Sous-menu déroulant Agences & extérieur (PC) --}}
                <div class="app-sidebar-nav-group nav-dropdown {{ request()->routeIs('equipes.*') ? 'is-open' : '' }}" data-nav-dropdown>
                    <button type="button" class="app-sidebar-nav-item nav-dropdown-trigger w-full text-left flex items-center justify-between gap-2 cursor-pointer border-0 bg-transparent" aria-expanded="{{ request()->routeIs('equipes.*') ? 'true' : 'false' }}" aria-controls="sidebar-dropdown-agences" id="sidebar-trigger-agences">
                        <span class="flex items-center gap-3">
                            <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></span>
                            <span>Agences & extérieur</span>
                        </span>
                        <span class="nav-dropdown-chevron w-4 h-4 flex-shrink-0 transition-transform duration-200" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></span>
                    </button>
                    <div class="nav-dropdown-panel" id="sidebar-dropdown-agences" role="region" aria-labelledby="sidebar-trigger-agences">
                        <a href="{{ route('equipes.index') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('equipes.index') || request()->routeIs('equipes.create') || request()->routeIs('equipes.edit') || request()->routeIs('equipes.membres') ? 'is-active' : '' }}">
                            <span>Liste des agences</span>
                        </a>
                        <a href="{{ route('equipes.attribution') }}" class="app-sidebar-nav-item pl-10 {{ request()->routeIs('equipes.attribution') ? 'is-active' : '' }}">
                            <span>Attribution agences</span>
                        </a>
                    </div>
                </div>
                @endif
                @if(auth()->user()->isFondateurPrincipal())
                <a href="{{ route('diagnostic.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('diagnostic.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></span>
                    <span>Diagnostic</span>
                </a>
                @endif
                <div class="app-sidebar-divider"></div>
                <a href="{{ route('aide.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('aide.index') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    <span>Aide & informations</span>
                </a>
                <a href="{{ route('documents-officiels.index') }}" class="app-sidebar-nav-item {{ request()->routeIs('documents-officiels.*') ? 'is-active' : '' }}">
                    <span class="app-sidebar-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
                    <span>Contrat et Règlement</span>
                </a>
            </nav>
            <div class="app-sidebar-footer">
                <div class="app-sidebar-user">
                    <div class="app-sidebar-user-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</div>
                    <div>
                        <p class="app-sidebar-user-name">{{ auth()->user()->name }}</p>
                        <p class="app-sidebar-user-role">{{ auth()->user()->getRoleLabel() }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <header class="app-header ultra-top-bar" id="app-header">
            <div class="app-header-search-wrap">
                <div class="app-header-search-box">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="search" class="app-header-search" placeholder="Rechercher..." aria-label="Rechercher">
                </div>
            </div>
            <div class="app-header-actions ultra-top-bar-actions">
                @if(auth()->user()->hasRoleOrAbove('agent') || auth()->user()->isCreateur())
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="app-header-icon-btn relative {{ request()->routeIs('messagerie.*') ? 'is-active' : '' }} {{ $unreadMessagesCount > 0 ? 'ultra-btn-message-pulse' : '' }}" aria-label="Messagerie" title="Messagerie" aria-haspopup="true" :aria-expanded="open" @click="open = !open">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @if($unreadMessagesCount > 0)
                        <span class="ultra-top-bar-badge">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>
                        @endif
                    </button>
                    <div class="notifications-dropdown-panel messagerie-dropdown-panel fixed left-1/2 top-16 -translate-x-1/2 w-[min(480px,calc(100vw-2rem))] rounded-xl border border-white/20 shadow-2xl z-[100] overflow-y-auto overflow-x-hidden max-h-[85vh] bg-[#0f172a]" style="background-color: #0f172a; display: none;" x-show="open" x-transition x-cloak>
                        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between bg-[#0f172a] flex-shrink-0">
                            <span class="font-semibold text-white text-sm">Messagerie</span>
                            <a href="{{ route('messagerie.index') }}" class="text-xs text-[#1877f2] hover:underline" @click="open = false">Voir tout</a>
                        </div>
                        <div class="max-h-[70vh] overflow-y-auto overflow-x-hidden bg-[#0f172a]">
                            @forelse($headerConversations as $msg)
                            @php $other = $msg->sender_id === auth()->id() ? $msg->receiver : $msg->sender; @endphp
                            @if($other)
                            <a href="{{ route('messagerie.conversation', $other) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/5 transition-colors break-words" @click="open = false">
                                <span class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 text-sm font-medium text-white/90">{{ strtoupper(mb_substr($other->name ?? '?', 0, 2)) }}</span>
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <p class="text-sm font-medium text-white truncate">{{ $other->name ?? 'Utilisateur' }}</p>
                                    <p class="text-sm text-[#94a3b8] leading-snug break-words" style="overflow-wrap: break-word; word-break: break-word;">{{ Str::limit($msg->contenu, 200) }}</p>
                                    <p class="text-xs text-[#64748b] mt-0.5">{{ $msg->created_at->diffForHumans() }}</p>
                                </div>
                                @if($msg->receiver_id === auth()->id() && !$msg->read_at)<span class="w-2 h-2 rounded-full bg-[#1877f2] flex-shrink-0 mt-2" aria-hidden="true"></span>@endif
                            </a>
                            @endif
                            @empty
                            <div class="px-4 py-8 text-center text-[#94a3b8] text-sm">Aucun message</div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t border-white/10 bg-[#0f172a]">
                            <a href="{{ route('messagerie.index') }}" class="block text-center text-sm text-[#1877f2] hover:underline py-2" @click="open = false">Voir la messagerie</a>
                        </div>
                    </div>
                </div>
                @endif
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="app-header-icon-btn ultra-top-bar-circle relative {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}" aria-label="Notifications ({{ $unreadNotificationsCount }} non lues)" title="Notifications" aria-haspopup="true" :aria-expanded="open" @click="open = !open">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadNotificationsCount > 0)
                        <span class="ultra-top-bar-badge">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                        @endif
                    </button>
                    <div class="notifications-dropdown-panel fixed left-1/2 top-16 -translate-x-1/2 w-[min(480px,calc(100vw-2rem))] rounded-xl border border-white/20 shadow-2xl z-[100] overflow-y-auto overflow-x-hidden max-h-[85vh] bg-[#0f172a]" style="background-color: #0f172a; display: none;" x-show="open" x-transition x-cloak>
                        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between bg-[#0f172a] flex-shrink-0">
                            <span class="font-semibold text-white text-sm">Notifications</span>
                            <a href="{{ route('notifications.index') }}" class="text-xs text-[#1877f2] hover:underline" @click="open = false">Voir tout</a>
                        </div>
                        <div class="max-h-[70vh] overflow-y-auto overflow-x-hidden bg-[#0f172a]">
                            @forelse($headerNotifications as $notif)
                            @php $data = is_array($notif->data) ? $notif->data : (array) $notif->data; @endphp
                            <a href="{{ route('notifications.read', $notif->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/5 transition-colors break-words {{ $notif->read_at ? 'opacity-80' : '' }}" @click="open = false">
                                <span class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </span>
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <p class="text-sm text-white leading-snug" style="overflow-wrap: break-word; word-break: break-word;">{{ $data['message'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-[#64748b] mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                @if(!$notif->read_at)<span class="w-2 h-2 rounded-full bg-[#1877f2] flex-shrink-0 mt-2" aria-hidden="true"></span>@endif
                            </a>
                            @empty
                            <div class="px-4 py-8 text-center text-[#94a3b8] text-sm">Aucune notification</div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t border-white/10 bg-[#0f172a]">
                            <a href="{{ route('notifications.index') }}" class="block text-center text-sm text-[#1877f2] hover:underline py-2" @click="open = false">Voir toutes les notifications</a>
                        </div>
                    </div>
                </div>
                <div class="app-header-dropdown-wrap" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="app-header-avatar-btn ultra-top-bar-avatar" aria-label="Menu compte" aria-haspopup="true" :aria-expanded="open" @click="open = !open">
                        <span class="ultra-avatar-initials">{{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
                        <svg class="ultra-avatar-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="app-header-dropdown-panel" role="menu" aria-label="Menu compte" x-show="open" x-transition x-cloak style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="app-header-dropdown-item" role="menuitem"><span class="app-header-dropdown-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span> Modifier le profil</a>
                        @if(auth()->user()->isCreateur())
                        <a href="{{ route('createurs.index') }}" class="app-header-dropdown-item {{ request()->routeIs('createurs.*') ? 'is-active' : '' }}" role="menuitem"><span class="app-header-dropdown-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span> Ma fiche</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="app-header-dropdown-item-form">
                            @csrf
                            <button type="submit" class="app-header-dropdown-item" role="menuitem"><span class="app-header-dropdown-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></span> Déconnexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <header class="md:hidden fixed top-0 left-0 right-0 h-14 z-30 ultra-header ultra-top-bar-mobile flex items-center justify-between px-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline">
                <span class="font-bold text-base text-white">Ultra</span>
                <div class="w-9 h-9 rounded-full bg-[#1877f2] flex items-center justify-center font-bold text-white text-sm flex-shrink-0">U</div>
            </a>
            <div class="flex items-center gap-1">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="relative flex items-center justify-center w-10 h-10 rounded-full text-white hover:text-white hover:bg-white/15 transition-colors {{ $unreadNotificationsCount > 0 ? 'ultra-btn-notif-mobile-pulse' : '' }}" aria-label="Notifications ({{ $unreadNotificationsCount }} non lues)" title="Notifications" @click="open = !open">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadNotificationsCount > 0)
                        <span class="ultra-top-bar-badge absolute top-0.5 right-0.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                        @endif
                    </button>
                    {{-- Fond sombre sur mobile quand le panneau est ouvert — clic pour fermer --}}
                    <div class="fixed inset-0 bg-black/50 z-[115] md:hidden" aria-hidden="true" x-show="open" x-transition style="display: none;" @click="open = false"></div>
                    <div class="notifications-dropdown-panel ultra-notif-mobile fixed top-[3.75rem] md:hidden inset-x-3 max-h-[80vh] rounded-xl border-2 border-white/25 shadow-2xl z-[120] overflow-y-auto overflow-x-hidden bg-[#0f172a] box-border" style="background-color: #0f172a; display: none;" x-show="open" x-transition x-cloak>
                        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between gap-2 bg-[#0f172a] flex-shrink-0 min-w-0">
                            <span class="font-semibold text-white text-sm min-w-0">Notifications</span>
                            <a href="{{ route('notifications.index') }}" class="text-xs text-[#1877f2] hover:underline flex-shrink-0 whitespace-nowrap" @click="open = false">Voir tout</a>
                        </div>
                        <div class="max-h-[75vh] overflow-y-auto overflow-x-hidden bg-[#0f172a]">
                            @forelse($headerNotifications as $notif)
                            @php $data = is_array($notif->data) ? $notif->data : (array) $notif->data; @endphp
                            <a href="{{ route('notifications.read', $notif->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-white/5 transition-colors {{ $notif->read_at ? 'opacity-80' : '' }} min-w-0" @click="open = false">
                                <span class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </span>
                                <div class="min-w-0 flex-1 overflow-hidden" style="max-width: 100%;">
                                    <p class="text-sm text-white leading-snug break-words" style="overflow-wrap: break-word; word-break: break-word; hyphens: auto;">{{ $data['message'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-[#64748b] mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                @if(!$notif->read_at)<span class="w-2 h-2 rounded-full bg-[#1877f2] flex-shrink-0 mt-2" aria-hidden="true"></span>@endif
                            </a>
                            @empty
                            <div class="px-4 py-8 text-center text-[#94a3b8] text-sm">Aucune notification</div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t border-white/10 bg-[#0f172a] min-w-0">
                            <a href="{{ route('notifications.index') }}" class="block text-center text-sm text-[#1877f2] hover:underline py-2 break-words" style="overflow-wrap: break-word;" @click="open = false">Voir toutes les notifications</a>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->hasRoleOrAbove('agent') || auth()->user()->isCreateur())
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="ultra-btn-message-mobile relative flex items-center justify-center w-10 h-10 rounded-full text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 {{ $unreadMessagesCount > 0 ? 'ultra-btn-message-pulse' : '' }}" aria-label="Messagerie" title="Messagerie" @click="open = !open">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @if($unreadMessagesCount > 0)
                        <span class="ultra-top-bar-badge">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>
                        @endif
                    </button>
                    <div class="notifications-dropdown-panel messagerie-dropdown-panel ultra-msg-mobile fixed top-[3.75rem] md:hidden inset-x-3 max-h-[55vh] rounded-xl border border-white/20 shadow-2xl z-[110] overflow-y-auto overflow-x-hidden bg-[#0f172a] box-border" style="background-color: #0f172a; display: none;" x-show="open" x-transition x-cloak>
                        <div class="px-3 py-2 border-b border-white/10 flex items-center justify-between bg-[#0f172a] flex-shrink-0">
                            <span class="font-semibold text-white text-xs">Messagerie</span>
                            <a href="{{ route('messagerie.index') }}" class="text-[10px] text-[#1877f2] hover:underline flex-shrink-0" @click="open = false">Voir tout</a>
                        </div>
                        <div class="max-h-[42vh] overflow-y-auto overflow-x-hidden bg-[#0f172a]">
                            @forelse($headerConversations as $msg)
                            @php $other = $msg->sender_id === auth()->id() ? $msg->receiver : $msg->sender; @endphp
                            @if($other)
                            <a href="{{ route('messagerie.conversation', $other) }}" class="flex items-center gap-2 px-3 py-2 hover:bg-white/5 transition-colors min-w-0" @click="open = false">
                                <span class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 text-[10px] font-medium text-white/90">{{ strtoupper(mb_substr($other->name ?? '?', 0, 2)) }}</span>
                                <div class="min-w-0 flex-1 overflow-hidden" style="max-width: 100%;">
                                    <p class="text-xs font-medium text-white truncate">{{ $other->name ?? 'Utilisateur' }}</p>
                                    <p class="text-[10px] text-[#94a3b8] break-words" style="overflow-wrap: break-word; word-break: break-word; hyphens: auto;">{{ Str::limit($msg->contenu, 150) }}</p>
                                </div>
                                <span class="text-[9px] text-[#64748b] flex-shrink-0">{{ $msg->created_at->diffForHumans() }}</span>
                                @if($msg->receiver_id === auth()->id() && !$msg->read_at)<span class="w-1.5 h-1.5 rounded-full bg-[#1877f2] flex-shrink-0" aria-hidden="true"></span>@endif
                            </a>
                            @endif
                            @empty
                            <div class="px-3 py-4 text-center text-[#94a3b8] text-xs">Aucun message</div>
                            @endforelse
                        </div>
                        <div class="px-3 py-1.5 border-t border-white/10 bg-[#0f172a]">
                            <a href="{{ route('messagerie.index') }}" class="block text-center text-[10px] text-[#1877f2] hover:underline py-1.5" @click="open = false">Voir la messagerie</a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </header>

        <div id="menu-bottom-sheet" class="ultra-bottom-sheet md:hidden" aria-hidden="true">
            <div class="ultra-bottom-sheet-overlay js-close-menu-sheet" aria-label="Fermer"></div>
            <div class="ultra-menu-panel ultra-bottom-sheet-panel menu-drawer-modern flex flex-col rounded-t-3xl">
                <div class="menu-drawer-header flex items-center justify-between h-14 px-5 flex-shrink-0 border-b border-white/10">
                    <span class="font-bold text-base text-white tracking-tight">Menu</span>
                    <button type="button" class="js-close-menu-sheet p-2.5 -mr-1 text-[#94a3b8] hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200" aria-label="Fermer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="menu-drawer-profile flex-shrink-0 px-5 py-4">
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#00d4ff] to-[#a78bfa] flex items-center justify-center font-bold text-white text-lg shadow-lg shadow-indigo-500/20 flex-shrink-0">{{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-white truncate text-base">{{ auth()->user()->name }}</p>
                            <p class="text-sm text-[#94a3b8] truncate mt-0.5">{{ auth()->user()->phone ?: auth()->user()->getRoleLabel() }}</p>
                        </div>
                    </div>
                </div>

                <div class="ultra-menu-panel-scroll flex-1 min-h-0 overflow-y-auto py-1">
                    <nav class="px-4 space-y-0.5">
                        <a href="{{ route('dashboard') }}" class="menu-drawer-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></span>
                            Tableau de bord
                        </a>
                        @if(auth()->user()->isFondateur())
                        {{-- Sous-menu déroulant Administration (mobile) --}}
                        <div class="nav-dropdown {{ (request()->routeIs('users.*') || request()->routeIs('password.generate-code*') || request()->routeIs('blacklist.*') || request()->routeIs('score-integrite.gestion') || request()->routeIs('import.*') || request()->routeIs('rapport-vendredi.*') || request()->routeIs('donnees-match.*')) ? 'is-open' : '' }}" data-nav-dropdown>
                            <button type="button" class="menu-drawer-dropdown-trigger w-full flex items-center justify-between gap-2 px-4 py-3 rounded-xl text-left text-[#94a3b8] text-xs font-semibold uppercase tracking-wider cursor-pointer border-0 bg-transparent hover:bg-white/5 hover:text-white transition-colors" aria-expanded="{{ (request()->routeIs('users.*') || request()->routeIs('import.*') || request()->routeIs('rapport-vendredi.*')) ? 'true' : 'false' }}" aria-controls="drawer-dropdown-admin" id="drawer-trigger-admin">
                                <span>Administration</span>
                                <span class="nav-dropdown-chevron w-4 h-4 flex-shrink-0 transition-transform duration-200" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></span>
                            </button>
                            <div class="nav-dropdown-panel" id="drawer-dropdown-admin" role="region" aria-labelledby="drawer-trigger-admin">
                                <a href="{{ route('users.index') }}" class="menu-drawer-link {{ request()->routeIs('users.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                            Utilisateurs
                        </a>
                        <a href="{{ route('password.generate-code.form') }}" class="menu-drawer-link {{ request()->routeIs('password.generate-code*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></span>
                            Générer un code mot de passe
                        </a>
                        <a href="{{ route('blacklist.index') }}" class="menu-drawer-link {{ request()->routeIs('blacklist.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></span>
                            Liste noire
                        </a>
                        @if(auth()->user()->isFondateurPrincipal())
                        <a href="{{ route('score-integrite.gestion') }}" class="menu-drawer-link {{ request()->routeIs('score-integrite.gestion') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></span>
                            Infractions (score)
                        </a>
                        <a href="{{ route('import.index') }}" class="menu-drawer-link {{ request()->routeIs('import.index') || request()->routeIs('import.store') || request()->routeIs('import.template') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg></span>
                            Import Excel
                        </a>
                        <a href="{{ route('import.corriger-heures-jours') }}" class="menu-drawer-link {{ request()->routeIs('import.corriger-heures-jours') || request()->routeIs('import.mettre-a-jour-heures-jours') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span>
                            Corriger heures et jours
                        </a>
                        <a href="{{ route('rapport-vendredi.index') }}" class="menu-drawer-link {{ request()->routeIs('rapport-vendredi.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
                            Rapport du mois
                        </a>
                        <a href="{{ route('donnees-match.index') }}" class="menu-drawer-link {{ request()->routeIs('donnees-match.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                            Données match (répertoire)
                        </a>
                                @endif
                            </div>
                        </div>
                        @elseif(!auth()->user()->isCreateur() && !auth()->user()->isAgent())
                        <a href="{{ route('users.index') }}" class="menu-drawer-link {{ request()->routeIs('users.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                            Utilisateurs
                        </a>
                        @if(auth()->user()->hasRoleOrAbove('manageur'))
                        <a href="{{ route('password.generate-code.form') }}" class="menu-drawer-link {{ request()->routeIs('password.generate-code*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></span>
                            Générer un code mot de passe
                        </a>
                        @endif
                        @endif
                        {{-- Matchs, Récompenses, Message, Formations : visible pour tous --}}
                        <a href="{{ route('matches.index') }}" class="menu-drawer-link {{ request()->routeIs('matches.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                            Matchs
                        </a>
                        @if(auth()->user()->isAgent())
                        <a href="{{ route('createurs.mes-createurs') }}" class="menu-drawer-link {{ request()->routeIs('createurs.mes-createurs') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                            Mes créateurs
                        </a>
                        @endif
                        <a href="{{ route('recompenses.index') }}" class="menu-drawer-link {{ request()->routeIs('recompenses.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg></span>
                            Récompenses
                        </a>
                        <a href="{{ route('regles.index') }}" class="menu-drawer-link {{ request()->routeIs('regles.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></span>
                            Le Message de l'agence
                        </a>
                        <a href="{{ route('formations.index') }}" class="menu-drawer-link {{ request()->routeIs('formations.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></span>
                            Nos Formations
                        </a>
                        @if(!auth()->user()->isCreateur() && !auth()->user()->isFondateur())
                        <a href="{{ route('blacklist.index') }}" class="menu-drawer-link {{ request()->routeIs('blacklist.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></span>
                            Liste noire
                        </a>
                        @endif
                        @if(auth()->user()->canSeeScoreIntegrite())
                        <a href="{{ route('score-integrite.index') }}" class="menu-drawer-link {{ request()->routeIs('score-integrite.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                            Score d'intégrité
                        </a>
                        @endif
                        {{-- Bonnes actions (score fidélité) : visible pour tous --}}
                        <a href="{{ route('score-fidelite.index') }}" class="menu-drawer-link {{ request()->routeIs('score-fidelite.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg></span>
                            Bonnes actions
                        </a>
                        @if(!auth()->user()->isFondateur() && !auth()->user()->isCreateur())
                        <a href="{{ route('rapport-vendredi.index') }}" class="menu-drawer-link {{ request()->routeIs('rapport-vendredi.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
                            Rapport du mois
                        </a>
                        @endif
                        @if(auth()->user()->isFondateurPrincipal())
                        {{-- Sous-menu déroulant Agences & extérieur (mobile) --}}
                        <div class="nav-dropdown {{ request()->routeIs('equipes.*') ? 'is-open' : '' }}" data-nav-dropdown>
                            <button type="button" class="menu-drawer-dropdown-trigger w-full flex items-center justify-between gap-2 px-4 py-3 rounded-xl text-left text-[#94a3b8] text-xs font-semibold uppercase tracking-wider cursor-pointer border-0 bg-transparent hover:bg-white/5 hover:text-white transition-colors" aria-expanded="{{ request()->routeIs('equipes.*') ? 'true' : 'false' }}" aria-controls="drawer-dropdown-agences" id="drawer-trigger-agences">
                                <span>Agences & extérieur</span>
                                <span class="nav-dropdown-chevron w-4 h-4 flex-shrink-0 transition-transform duration-200" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></span>
                            </button>
                            <div class="nav-dropdown-panel" id="drawer-dropdown-agences" role="region" aria-labelledby="drawer-trigger-agences">
                                <a href="{{ route('equipes.index') }}" class="menu-drawer-link {{ request()->routeIs('equipes.index') || request()->routeIs('equipes.create') || request()->routeIs('equipes.edit') || request()->routeIs('equipes.membres') ? 'is-active' : '' }}">
                                    <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></span>
                                    Liste des agences
                                </a>
                                <a href="{{ route('equipes.attribution') }}" class="menu-drawer-link {{ request()->routeIs('equipes.attribution') ? 'is-active' : '' }}">
                                    <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                                    Attribution agences
                                </a>
                            </div>
                        </div>
                        @endif
                        @if(auth()->user()->isFondateurPrincipal())
                        <a href="{{ route('diagnostic.index') }}" class="menu-drawer-link {{ request()->routeIs('diagnostic.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></span>
                            Diagnostic
                        </a>
                        @endif
                        <a href="{{ route('aide.index') }}" class="menu-drawer-link {{ request()->routeIs('aide.index') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                            Aide & informations
                        </a>
                        <a href="{{ route('documents-officiels.index') }}" class="menu-drawer-link {{ request()->routeIs('documents-officiels.*') ? 'is-active' : '' }}">
                            <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
                            Contrat et Règlement
                        </a>
                        {{-- Déconnexion dans la liste (toujours visible au scroll) --}}
                        <form action="{{ route('logout') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="menu-drawer-signout-link menu-drawer-link w-full text-left border-0 bg-transparent cursor-pointer flex items-center gap-3 px-4 py-3 rounded-xl transition-colors">
                                <span class="menu-drawer-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></span>
                                Déconnexion
                            </button>
                        </form>
                    </nav>
                </div>

                <div class="menu-drawer-signout flex-shrink-0 px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] border-t border-white/10 bg-white/5">
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="menu-drawer-logout-btn w-full flex items-center justify-center gap-2.5 py-3.5 px-4 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-[#f43f5e] to-[#e11d48] hover:from-[#e11d48] hover:to-[#be123c] shadow-lg shadow-rose-500/25 hover:shadow-rose-500/30 active:scale-[0.98] transition-all duration-200 border-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- OneSignal (notifications push) --}}
        <main class="app-main ultra-main ultra-main-modern dashboard-main flex-1 overflow-auto p-4 {{ request()->routeIs('messagerie.*') ? 'pb-2' : 'pb-24' }} md:pb-8 min-h-screen" id="main-content">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-xl bg-neon-green/10 text-neon-green border border-neon-green/30">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="mb-4 p-3 rounded-xl bg-neon-orange/10 text-neon-orange border border-neon-orange/30">{{ session('warning') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 rounded-xl bg-neon-pink/10 text-neon-pink border border-neon-pink/30">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-neon-pink/10 text-neon-pink border border-neon-pink/30">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <nav role="navigation" aria-label="Navigation principale" class="ultra-bottom-nav-bar ultra-bottom-nav md:hidden flex items-center justify-around h-[75px] px-2" style="position:fixed; top:auto; bottom:0; left:0; right:0; z-index:40; padding-bottom:env(safe-area-inset-bottom,0);">
        <a href="{{ route('dashboard') }}" aria-label="Accueil" class="flex flex-col items-center justify-center flex-1 py-2 transition-all {{ request()->routeIs('dashboard') ? 'nav-link-active-1' : 'text-white/50 hover:text-white/80' }}">
            <svg class="w-7 h-7 mb-1" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[0.65rem] font-medium">Accueil</span>
        </a>
        @if(auth()->user()->isCreateur())
        <a href="{{ route('createurs.index') }}" aria-label="Ma fiche" class="flex flex-col items-center justify-center flex-1 py-2 transition-all {{ request()->routeIs('createurs.show') ? 'nav-link-active-2' : 'text-white/50 hover:text-white/80' }}">
            <svg class="w-7 h-7 mb-1" fill="{{ request()->routeIs('createurs.show') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[0.65rem] font-medium">Ma fiche</span>
        </a>
        @elseif(auth()->user()->isAgent())
        <a href="{{ route('createurs.mes-createurs') }}" aria-label="Mes créateurs" class="flex flex-col items-center justify-center flex-1 py-2 transition-all {{ request()->routeIs('createurs.mes-createurs') ? 'nav-link-active-2' : 'text-white/50 hover:text-white/80' }}">
            <svg class="w-7 h-7 mb-1" fill="{{ request()->routeIs('createurs.mes-createurs') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-[0.65rem] font-medium">Mes créateurs</span>
        </a>
        @else
        <a href="{{ route('users.index') }}" aria-label="Utilisateurs" class="flex flex-col items-center justify-center flex-1 py-2 transition-all {{ request()->routeIs('users.*') ? 'nav-link-active-2' : 'text-white/50 hover:text-white/80' }}">
            <svg class="w-7 h-7 mb-1" fill="{{ request()->routeIs('users.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-[0.65rem] font-medium">Utilisateurs</span>
        </a>
        @endif
        <button type="button" aria-label="Menu" class="js-open-menu-sheet flex flex-col items-center justify-center flex-1 py-2 text-white/50 hover:text-white/80 transition-all">
            <svg class="w-7 h-7 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="text-[0.65rem] font-medium">Menu</span>
        </button>
    </nav>

    <script>
        window.togglePassword = function(inputId, btn) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var open = btn.querySelector('.eye-open');
            var closed = btn.querySelector('.eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                if (open) open.classList.add('hidden');
                if (closed) closed.classList.remove('hidden');
                btn.setAttribute('title', 'Masquer le mot de passe');
                btn.setAttribute('aria-label', 'Masquer le mot de passe');
            } else {
                input.type = 'password';
                if (open) open.classList.remove('hidden');
                if (closed) closed.classList.add('hidden');
                btn.setAttribute('title', 'Afficher le mot de passe');
                btn.setAttribute('aria-label', 'Afficher le mot de passe');
            }
        };
        (function() {
            document.querySelectorAll('[data-nav-dropdown]').forEach(function(dropdown) {
                var trigger = dropdown.querySelector('.nav-dropdown-trigger, .menu-drawer-dropdown-trigger');
                var panel = dropdown.querySelector('.nav-dropdown-panel');
                if (!trigger || !panel) return;
                trigger.addEventListener('click', function() {
                    var isOpen = dropdown.classList.toggle('is-open');
                    trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            });
        })();
        (function() {
            var sheet = document.getElementById('menu-bottom-sheet');
            if (!sheet) return;
            function openMenu() {
                sheet.classList.add('is-open');
                sheet.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
            function closeMenu() {
                sheet.classList.remove('is-open');
                sheet.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
            document.querySelectorAll('.js-open-menu-sheet').forEach(function(btn) {
                btn.addEventListener('click', openMenu);
            });
            document.querySelectorAll('.js-close-menu-sheet').forEach(function(el) {
                el.addEventListener('click', closeMenu);
            });
            // Fermer le tiroir au clic sur un lien (uniquement "click" pour ne pas bloquer la navigation)
            sheet.addEventListener('click', function(e) {
                var link = e.target.closest('a[href]');
                if (link && link.getAttribute('href') && link.getAttribute('href').trim().indexOf('#') !== 0) {
                    closeMenu();
                }
            });
        })();
        // Header dropdowns gérés par Alpine.js
    </script>
    {{-- OneSignal : initialisation (notifications push) --}}
    @auth
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            await OneSignal.init({
                appId: "fb89cae2-ef53-469b-abe9-7503d5526601",
                safari_web_id: "web.onesignal.auto.370dd028-ab8a-4720-9087-4e2c917686de",
                serviceWorkerParam: { path: "/OneSignalSDKWorker.js" },
                notifyButton: { enable: false },
            });
        });
    </script>
    @endauth
    @stack('scripts')
</body>
</html>
