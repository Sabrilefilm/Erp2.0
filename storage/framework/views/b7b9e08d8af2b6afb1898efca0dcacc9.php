<?php $__env->startSection('title', 'Tableau de bord'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Hero greeting : sobre et lisible ────────────────── */
.db-hero {
    border-radius: 16px;
    padding: 24px 28px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
}
.db-hero-orb { display: none; }
.db-avatar {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: #1877f2;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.125rem; font-weight: 700; color: #fff;
    flex-shrink: 0;
    letter-spacing: 0.02em;
}
.db-hero .db-hero-greeting { font-size: 0.75rem; font-weight: 500; color: #94a3b8; text-transform: none; letter-spacing: 0; }
.db-hero .db-hero-name { font-size: 1.5rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; }
.db-hero .db-hero-role { font-size: 0.875rem; color: #64748b; }

/* ── KPI cards ──────────────────────────────────────── */
.kpi-card {
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: border-color .2s, transform .2s, box-shadow .2s;
    position: relative;
    overflow: hidden;
}
.kpi-card:hover {
    border-color: rgba(255,255,255,0.15);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.35);
}
.kpi-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    border-radius: 16px 16px 0 0;
    opacity: 0.7;
}
.kpi-blue::after  { background: linear-gradient(90deg,#00d4ff,transparent); }
.kpi-purple::after { background: linear-gradient(90deg,#b794f6,transparent); }
.kpi-green::after  { background: linear-gradient(90deg,#00ff88,transparent); }
.kpi-amber::after  { background: linear-gradient(90deg,#fbbf24,transparent); }

.kpi-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.kpi-value {
    font-size: 28px; font-weight: 800;
    line-height: 1; color: #fff;
    letter-spacing: -0.5px;
}
.kpi-label {
    font-size: 12px; color: rgba(255,255,255,0.4);
    font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em;
}

/* ── Quick access cards ─────────────────────────────── */
.qa-card {
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.03);
    padding: 14px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 7px;
    text-decoration: none;
    transition: all .22s ease;
    position: relative;
    overflow: hidden;
    text-align: center;
}
.qa-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    opacity: 0;
    transition: opacity .22s;
}
.qa-card:hover { transform: translateY(-3px); text-decoration: none; }
.qa-card:hover::before { opacity: 1; }

.qa-blue { --qa-c: #00d4ff; }
.qa-purple { --qa-c: #b794f6; }
.qa-green { --qa-c: #00ff88; }
.qa-amber { --qa-c: #fbbf24; }
.qa-pink { --qa-c: #ff6b9d; }
.qa-slate { --qa-c: #94a3b8; }

.qa-card:hover { border-color: color-mix(in srgb, var(--qa-c) 40%, transparent); box-shadow: 0 8px 24px color-mix(in srgb, var(--qa-c) 15%, transparent); }
.qa-card::before { background: color-mix(in srgb, var(--qa-c) 6%, transparent); }

.qa-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    background: color-mix(in srgb, var(--qa-c) 18%, transparent);
    color: var(--qa-c);
    transition: transform .22s, box-shadow .22s;
}
.qa-card:hover .qa-icon {
    transform: scale(1.08);
    box-shadow: 0 0 16px color-mix(in srgb, var(--qa-c) 28%, transparent);
}
.qa-label { font-size: 12px; font-weight: 600; color: #fff; }
.qa-desc { font-size: 10px; color: rgba(255,255,255,0.3); margin-top: -3px; }

/* ── Section titles ─────────────────────────────────── */
.db-section-title {
    font-size: 13px; font-weight: 700;
    color: rgba(255,255,255,0.35);
    text-transform: uppercase; letter-spacing: 0.1em;
    margin-bottom: 14px;
}

/* ── Progress bar ───────────────────────────────────── */
.db-progress-track {
    height: 6px; border-radius: 9999px;
    background: rgba(255,255,255,0.08);
    overflow: hidden;
}
.db-progress-fill {
    height: 100%; border-radius: 9999px;
    background: linear-gradient(90deg, #00d4ff, #b794f6);
    transition: width .5s cubic-bezier(.22,.68,0,1);
}

/* ── Animations ─────────────────────────────────────── */
.db-fade { opacity:0; transform:translateY(10px); animation:db-in .4s ease forwards; }
.db-fade:nth-child(1){animation-delay:.05s} .db-fade:nth-child(2){animation-delay:.10s}
.db-fade:nth-child(3){animation-delay:.15s} .db-fade:nth-child(4){animation-delay:.20s}
.db-fade:nth-child(5){animation-delay:.25s} .db-fade:nth-child(6){animation-delay:.30s}
@keyframes db-in { to { opacity:1; transform:translateY(0); } }

/* Stat stream card */
.stream-stat {
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.04);
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 14px;
}

/* Mon encadrement — compact, en petit */
.db-encadrement {
    border-radius: 12px;
    border: 1px solid rgba(0,212,255,0.12);
    background: linear-gradient(135deg, rgba(0,212,255,0.06), rgba(148,163,184,0.04));
    padding: 10px 12px;
    position: relative;
    overflow: hidden;
}

/* Message agence + campagne TikTok (compact, texte petit) */
.db-agency-box {
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.03);
    padding: 12px 14px;
}
.db-agency-box-title {
    font-size: 11px;
    font-weight: 700;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    display: flex;
    align-items: center;
    gap: 8px;
}
.db-agency-box-content {
    margin-top: 8px;
    font-size: 12px;
    line-height: 1.45;
    color: rgba(255,255,255,0.8);
}
.db-agency-box-content .muted {
    color: rgba(255,255,255,0.35);
    font-size: 11px;
}
.db-agency-box-actions {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.db-mini-link {
    font-size: 11px;
    font-weight: 600;
    color: #00d4ff;
    text-decoration: none;
}
.db-mini-link:hover { color: #7dd3fc; text-decoration: none; }
.db-encadrement::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 2px;
    height: 100%;
    background: linear-gradient(180deg, #00d4ff, #64748b);
    border-radius: 2px 0 0 2px;
}
.db-encadrement-title {
    font-size: 10px;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.02em;
    margin-bottom: 8px;
    padding-left: 2px;
}
.db-encadrement-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 8px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    margin-bottom: 5px;
}
.db-encadrement-row:last-child { margin-bottom: 0; }
.db-encadrement-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(0,212,255,0.12);
    color: #00d4ff;
}
.db-encadrement-icon svg { width: 14px; height: 14px; }
.db-encadrement-row.equipe .db-encadrement-icon {
    background: rgba(148,163,184,0.15);
    color: #94a3b8;
}
.db-encadrement-row.whatsapp .db-encadrement-icon {
    background: rgba(37,211,102,0.2);
    color: #25d366;
}
.db-encadrement-row.whatsapp .db-encadrement-value a {
    color: #25d366;
    text-decoration: none;
    font-weight: 600;
}
.db-encadrement-row.whatsapp .db-encadrement-value a:hover {
    text-decoration: underline;
}
.db-encadrement-label {
    font-size: 9px;
    font-weight: 500;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1px;
}
.db-encadrement-value {
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    letter-spacing: -0.01em;
}
.db-encadrement-row .shrink-0.px-3 { padding-left: 8px; padding-right: 8px; font-size: 10px; }

/* Bonnes actions (score) : petit badge fun sur l'accueil créateur */
.db-fidelite-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.06));
    border: 1px solid rgba(34,197,94,0.25);
    color: #86efac;
    font-size: 11px;
    font-weight: 600;
    transition: border-color .2s, box-shadow .2s, transform .15s;
}
.db-fidelite-badge:hover {
    border-color: rgba(34,197,94,0.45);
    box-shadow: 0 0 12px rgba(34,197,94,0.15);
    transform: scale(1.02);
    color: #bbf7d0;
}
.db-fidelite-emoji { font-size: 13px; line-height: 1; }
.db-fidelite-text { letter-spacing: 0.02em; }
.db-fidelite-detail { font-weight: 500; }
/* Score 0 = discret + mini animation pour donner envie de gagner */
.db-fidelite-badge.is-zero {
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.55);
    animation: db-zero-encourage 2.5s ease-in-out infinite;
}
.db-fidelite-badge.is-zero:hover { color: rgba(255,255,255,0.8); animation: none; }
@keyframes db-zero-encourage {
    0%, 100% { opacity: 0.9; box-shadow: 0 0 0 rgba(34,197,94,0); }
    50% { opacity: 1; box-shadow: 0 0 14px rgba(34,197,94,0.12); }
}
/* Score > 0 = animation +N au chargement */
.db-fidelite-plus {
    display: inline-block;
    animation: db-fidelite-pop 0.6s ease-out;
}
@keyframes db-fidelite-pop {
    0% { opacity: 0; transform: scale(0.5); }
    50% { transform: scale(1.15); }
    100% { opacity: 1; transform: scale(1); }
}
/* Badge score d'intégrité (à côté des bonnes actions) */
.db-integrite-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgba(14,165,233,0.15), rgba(14,165,233,0.06));
    border: 1px solid rgba(14,165,233,0.25);
    color: #7dd3fc;
    font-size: 11px;
    font-weight: 600;
    transition: border-color .2s, box-shadow .2s, transform .15s;
}
.db-integrite-badge:hover {
    border-color: rgba(14,165,233,0.45);
    box-shadow: 0 0 12px rgba(14,165,233,0.15);
    transform: scale(1.02);
    color: #bae6fd;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php if(!empty($rapportVendrediManquant)): ?>
<div class="mb-4 rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 flex items-center justify-between gap-4 flex-wrap">
    <p class="text-amber-200 text-sm font-medium">Rapport de la semaine non rempli. Merci de le remplir pour que la direction puisse vous accompagner et vous donner des consignes adaptées.</p>
    <a href="<?php echo e(route('rapport-vendredi.index')); ?>" class="shrink-0 px-4 py-2 rounded-xl bg-amber-500/30 hover:bg-amber-500/40 text-amber-200 font-semibold text-sm transition-colors">Remplir le rapport</a>
</div>
<?php endif; ?>

<?php $dashUnread = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count(); ?>
<?php if($dashUnread > 0 && !request()->routeIs('messagerie.*')): ?>
<a href="<?php echo e(route('messagerie.index')); ?>"
   class="flex items-center gap-3 mb-4 px-4 py-3 rounded-2xl no-underline group transition-all"
   style="background:linear-gradient(135deg,rgba(0,212,255,0.12),rgba(14,165,233,0.08));border:1px solid rgba(0,212,255,0.25);">
    <span class="relative shrink-0">
        <svg class="w-5 h-5 text-[#00d4ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <span style="position:absolute;top:-5px;right:-5px;min-width:16px;height:16px;background:#00d4ff;color:#0a0e27;font-size:9px;font-weight:800;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px;"><?php echo e($dashUnread > 99 ? '99+' : $dashUnread); ?></span>
    </span>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-white leading-none">
            <?php echo e($dashUnread === 1 ? 'Vous avez 1 message non lu' : "Vous avez {$dashUnread} messages non lus"); ?>

        </p>
        <p class="text-[11px] text-[#00d4ff]/60 mt-0.5">Cliquez pour ouvrir la messagerie</p>
    </div>
    <svg class="w-4 h-4 text-[#00d4ff]/50 group-hover:text-[#00d4ff] group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
</a>
<?php endif; ?>

<?php if(auth()->user()->isCreateur()): ?>

<?php
    $cr = $createurRecord ?? null;
    $hasData = ($jours ?? null) !== null || ($heures ?? null) !== null || ($diamants ?? null) !== null;
    $objectifJours = 7; $objectifHeures = 16;
    $pctJours  = $objectifJours > 0 && isset($jours)   && $jours   !== null ? min(100, (int)round(($jours   / $objectifJours)   * 100)) : 0;
    $pctHeures = $objectifHeures > 0 && isset($heures) && $heures !== null ? min(100, (int)round(($heures / $objectifHeures) * 100)) : 0;
    $pctGlobal = $hasData ? (int)round(($pctJours + $pctHeures) / 2) : 0;
    $initials  = strtoupper(mb_substr(auth()->user()->name, 0, 2));
?>
<div class="space-y-5">

    
    <?php if(auth()->user()->isBirthdayToday()): ?>
    <div class="rounded-2xl overflow-hidden bg-gradient-to-r from-pink-500/25 via-rose-500/20 to-amber-500/20 border border-pink-400/30 p-4 md:p-5 animate-pulse" style="animation-duration: 2s;">
        <div class="flex items-center gap-3">
            <span class="text-4xl" aria-hidden="true">🎂</span>
            <div>
                <p class="text-lg font-bold text-white">Joyeux anniversaire, <?php echo e(auth()->user()->name); ?> !</p>
                <p class="text-sm text-pink-200/90">Toute l'équipe te souhaite une belle journée.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="db-hero db-fade">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="db-avatar"><?php echo e($initials); ?></div>
            <div class="flex-1 min-w-0">
                <p class="db-hero-greeting">Bonjour</p>
                <h1 class="db-hero-name mt-0.5"><?php echo e(auth()->user()->name); ?></h1>
                <p class="db-hero-role mt-0.5">Données de stream</p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <?php if(isset($scoreFideliteMois) && $scoreFideliteMois !== null): ?>
                    <a href="<?php echo e(route('score-fidelite.index')); ?>" class="db-fidelite-badge inline-flex items-center gap-1.5 no-underline group <?php echo e($scoreFideliteMois == 0 ? 'is-zero' : ''); ?>">
                        <span class="db-fidelite-emoji">🎁</span>
                        <?php if($scoreFideliteMois == 0): ?>
                        <span class="db-fidelite-text">0 pt ce mois</span>
                        <?php else: ?>
                        <span class="db-fidelite-plus db-fidelite-text">+<?php echo e($scoreFideliteMois); ?> pts ce mois</span>
                        <?php endif; ?>
                        <span class="db-fidelite-detail text-[10px] opacity-70 group-hover:opacity-100">→ détail</span>
                    </a>
                    <?php endif; ?>
                    <?php if(isset($scoreIntegrite)): ?>
                    <a href="<?php echo e(route('score-integrite.index')); ?>" class="db-integrite-badge inline-flex items-center gap-1.5 no-underline group">
                        <span class="db-fidelite-emoji">📊</span>
                        <span class="db-fidelite-text">Intégrité <?php echo e($scoreIntegrite); ?>/100</span>
                        <span class="db-fidelite-detail text-[10px] opacity-70 group-hover:opacity-100">→ détail</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?php echo e(route('formations.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1877f2] hover:bg-[#166fe5] text-white text-sm font-semibold transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Formations
            </a>
        </div>
    </div>

    
    <?php if(isset($scoreIntegrite) && $scoreIntegrite >= 30 && $scoreIntegrite < 60): ?>
    <a href="<?php echo e(route('score-integrite.index')); ?>" class="db-fade flex items-center gap-4 p-4 rounded-2xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/15 transition-colors no-underline group">
        <span class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center shrink-0 text-2xl">📊</span>
        <div class="flex-1 min-w-0">
            <p class="text-amber-400 font-semibold text-sm">Score d'intégrité : <?php echo e($scoreIntegrite); ?>/100 (zone orange)</p>
            <p class="text-amber-400/80 text-xs mt-0.5">Tu peux mieux faire. Clique pour voir le détail et améliorer ton score.</p>
        </div>
        <svg class="w-5 h-5 text-amber-400 group-hover:translate-x-0.5 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <?php endif; ?>

    
    <div class="db-fade rounded-2xl border border-white/08 bg-white/[0.03] overflow-hidden">
        
        <div class="flex items-center justify-between px-4 pt-4 pb-3">
            <p class="text-[11px] font-bold uppercase tracking-widest text-white/30">Activité stream <span class="normal-case font-normal text-white/25">(données import)</span></p>
            <?php if($cr && $moisDisponibles && $moisDisponibles->isNotEmpty()): ?>
            <form id="form-mois-dashboard" method="get" action="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-1.5">
                <select name="annee" class="rounded-md bg-white/5 border border-white/10 text-white text-[11px] px-2 py-1 focus:outline-none focus:border-[#00d4ff]/50">
                    <?php $__currentLoopData = $moisDisponibles->pluck('annee')->unique()->sortDesc()->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($y); ?>" <?php echo e((int)($annee ?? 0) === (int)$y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="mois" class="rounded-md bg-white/5 border border-white/10 text-white text-[11px] px-2 py-1 focus:outline-none focus:border-[#00d4ff]/50">
                    <?php $__currentLoopData = $moisDisponibles->where('annee', $annee); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m['mois']); ?>" <?php echo e((int)($mois ?? 0) === (int)$m['mois'] ? 'selected' : ''); ?>><?php echo e($m['libelle']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <script>
            (function(){
                var f = document.getElementById('form-mois-dashboard');
                if (!f) return;
                var base = f.getAttribute('action') || '';
                function go(){ var a = f.querySelector('[name=annee]').value; var m = f.querySelector('[name=mois]').value; window.location.href = base + (base.indexOf('?') >= 0 ? '&' : '?') + 'annee=' + encodeURIComponent(a) + '&mois=' + encodeURIComponent(m); }
                f.querySelectorAll('select').forEach(function(s){ s.addEventListener('change', go); });
            })();
            </script>
            <?php endif; ?>
        </div>
        
        <div class="flex gap-2 px-3 py-2">
            <div class="flex-1 flex flex-col items-center gap-1 py-3 px-2 text-center rounded-xl bg-white/[0.03]">
                <span class="text-base">📅</span>
                <span class="text-lg font-bold text-white leading-none"><?php echo e(isset($jours) && $jours !== null ? $jours : '—'); ?></span>
                <span class="text-[10px] text-white/35 font-semibold uppercase tracking-wider">Jours</span>
            </div>
            <div class="flex-1 flex flex-col items-center gap-1 py-3 px-2 text-center rounded-xl bg-white/[0.03]">
                <span class="text-base">⏱️</span>
                <span class="text-lg font-bold text-white leading-none"><?php echo e(isset($heures) && $heures !== null ? \App\Support\HeuresHelper::format((float) $heures) : '—'); ?></span>
                <span class="text-[10px] text-white/35 font-semibold uppercase tracking-wider">Heures</span>
            </div>
            <div class="flex-1 flex flex-col items-center gap-1 py-3 px-2 text-center rounded-xl bg-white/[0.03]">
                <span class="text-base">💎</span>
                <span class="text-lg font-bold text-white leading-none"><?php echo e(isset($diamants) && $diamants !== null ? number_format($diamants, 0, ',', ' ') : '—'); ?></span>
                <span class="text-[10px] text-white/35 font-semibold uppercase tracking-wider">Diamants</span>
            </div>
        </div>
        
        <?php
            $pctColor = $pctGlobal < 30 ? '#ef4444' : ($pctGlobal < 50 ? '#f59e0b' : '#e2e8f0');
            $pctEmoji = $pctGlobal < 30 ? '🔴' : ($pctGlobal < 50 ? '🟠' : '✅');
        ?>
        <div class="px-4 pb-4 pt-2">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] text-white/40"><?php echo e($objectifJours); ?>j - <?php echo e($objectifHeures); ?>h objectif</span>
                <span class="text-sm font-bold inline-flex items-center gap-1.5" style="color: <?php echo e($pctColor); ?>"><span aria-hidden="true"><?php echo e($pctEmoji); ?></span><?php echo e($pctGlobal); ?>%</span>
            </div>
            <div class="db-progress-track">
                <div class="db-progress-fill" style="width:<?php echo e(min(100,$pctGlobal)); ?>%; background: <?php echo e($pctColor); ?>;"></div>
            </div>
        </div>
    </div>

    
    <?php if($cr && ($cr->agent || $cr->equipe)): ?>
    <div class="db-encadrement db-fade">
        <p class="db-encadrement-title">Mon encadrement</p>
        <?php if($cr->agent): ?>
        <div class="db-encadrement-row">
            <span class="db-encadrement-icon" aria-hidden="true">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            <div>
                <div class="db-encadrement-label">Agent</div>
                <div class="db-encadrement-value"><?php echo e($cr->agent->name); ?></div>
            </div>
        </div>
        <?php
            $agentPhone = $cr->agent->phone ?? '';
            $waNumber = preg_replace('/\D/', '', (string) $agentPhone);
            if (str_starts_with($waNumber, '0') && strlen($waNumber) === 10) {
                $waNumber = '33' . substr($waNumber, 1);
            } elseif ($waNumber !== '' && strlen($waNumber) < 10) {
                $waNumber = '';
            }
            $waUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber : null;
        ?>
        <div class="db-encadrement-row whatsapp">
            <span class="db-encadrement-icon" aria-hidden="true">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </span>
            <div class="flex-1 min-w-0">
                <div class="db-encadrement-label">WhatsApp</div>
                <div class="db-encadrement-value">
                    <?php if($waUrl): ?>
                    <a href="<?php echo e($waUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($cr->agent->phone); ?></a>
                    <?php else: ?>
                    <span class="text-white/50">Non renseigné</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($waUrl): ?>
            <a href="<?php echo e($waUrl); ?>" target="_blank" rel="noopener noreferrer" class="shrink-0 px-3 py-1.5 rounded-lg bg-[#25d366]/20 text-[#25d366] text-xs font-semibold hover:bg-[#25d366]/30 transition-colors">Contacter</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($cr->equipe): ?>
        <div class="db-encadrement-row equipe">
            <span class="db-encadrement-icon" aria-hidden="true">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <div>
                <div class="db-encadrement-label">Équipe</div>
                <div class="db-encadrement-value"><?php echo e($cr->equipe->nom); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <div class="db-fade mt-2">
        <p class="db-section-title">Accès rapide</p>
        <div class="flex gap-3">
            <a href="<?php echo e(route('matches.index')); ?>" class="qa-card qa-purple flex-1">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                <div class="qa-label">Matchs</div>
                <div class="qa-desc">Planning live</div>
            </a>
            <a href="<?php echo e(route('recompenses.index')); ?>" class="qa-card qa-green flex-1">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg></div>
                <div class="qa-label">Récompenses</div>
                <div class="qa-desc">Mes gains</div>
            </a>
        </div>
    </div>

    
    <div class="db-fade mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="db-agency-box">
            <div class="db-agency-box-title"><span aria-hidden="true">💬</span> Message de l'agence</div>
            <div class="db-agency-box-content">
                <?php if(!empty($messageAgence)): ?>
                    <div class="font-semibold text-white/90"><?php echo e($messageAgence->titre); ?></div>
                    <div class="muted mt-1"><?php echo e(\Illuminate\Support\Str::limit(trim($messageAgence->contenu), 140)); ?></div>
                <?php else: ?>
                    <div class="muted">Aucun message pour le moment.</div>
                <?php endif; ?>
            </div>
            <div class="db-agency-box-actions">
                <a class="db-mini-link" href="<?php echo e(route('regles.index')); ?>">Voir tous les messages</a>
            </div>
        </div>

        <div class="db-agency-box">
            <div class="db-agency-box-title"><span aria-hidden="true">🎯</span> Campagne TikTok</div>
            <div class="db-agency-box-content">
                <div class="muted">Accède aux contenus et consignes liés aux campagnes TikTok.</div>
            </div>
            <div class="db-agency-box-actions">
                <a class="db-mini-link" href="<?php echo e(route('formations.index', $hasCatalogueTiktok ? ['catalogue' => 'tiktok'] : [])); ?>">Ouvrir</a>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<?php
    $vues      = $stats['vues_totales'] ?? 0;
    $followers = $stats['followers_totaux'] ?? 0;
    $createurs = $stats['createurs'] ?? 0;
    $equipesCount = $stats['equipes'] ?? 0;
    $vuesAffichage      = $vues >= 1000000 ? number_format($vues/1000000,1).'M' : ($vues >= 1000 ? number_format($vues/1000,0).'k' : number_format($vues,0,',',' '));
    $followersAffichage = $followers >= 1000000 ? number_format($followers/1000000,1).'M' : ($followers >= 1000 ? number_format($followers/1000,0).'k' : number_format($followers,0,',',' '));
    $initials = strtoupper(mb_substr(auth()->user()->name, 0, 2));
?>
<div class="space-y-5">

    
    <div class="db-hero db-fade">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="db-avatar"><?php echo e($initials); ?></div>
            <div class="flex-1 min-w-0">
                <p class="db-hero-greeting">Bonjour</p>
                <h1 class="db-hero-name mt-0.5"><?php echo e(auth()->user()->name); ?></h1>
                <p class="db-hero-role mt-0.5"><?php echo e(auth()->user()->getRoleLabel()); ?></p>
            </div>
            <a href="<?php echo e(route('createurs.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1877f2] hover:bg-[#166fe5] text-white text-sm font-semibold transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Créateurs
            </a>
        </div>
    </div>

    
    <div class="db-fade rounded-xl border border-white/10 bg-white/[0.03] p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#1877f2]/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#1877f2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-white tabular-nums"><?php echo e($createurs); ?></p>
                    <p class="text-xs text-[#94a3b8]">Créateurs</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#8b5cf6]/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-white tabular-nums"><?php echo e($vuesAffichage); ?></p>
                    <p class="text-xs text-[#94a3b8]">Vues</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-white tabular-nums"><?php echo e($followersAffichage); ?></p>
                    <p class="text-xs text-[#94a3b8]">Followers</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-white tabular-nums"><?php echo e($equipesCount); ?></p>
                    <p class="text-xs text-[#94a3b8]">Équipes</p>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(!auth()->user()->isCreateur()): ?>
    <div class="db-fade mt-2">
        <p class="db-section-title">Accès rapide</p>
        
        <div class="flex gap-3 overflow-x-auto pb-1 -mx-1 px-1">
            <a href="<?php echo e(route('createurs.index')); ?>" class="qa-card qa-blue shrink-0 w-20">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                <div class="qa-label">Créateurs</div>
            </a>
            <?php if(auth()->user()->hasRoleOrAbove('agent') || auth()->user()->isCreateur()): ?>
            <a href="<?php echo e(route('matches.index')); ?>" class="qa-card qa-purple shrink-0 w-20">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                <div class="qa-label">Matchs</div>
            </a>
            <a href="<?php echo e(route('recompenses.index')); ?>" class="qa-card qa-green shrink-0 w-20">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg></div>
                <div class="qa-label">Récompenses</div>
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('messagerie.index')); ?>" class="qa-card qa-pink shrink-0 w-20">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
                <div class="qa-label">Messages</div>
            </a>
            <?php if(auth()->user()->isFondateurPrincipal()): ?>
            <a href="<?php echo e(route('equipes.index')); ?>" class="qa-card qa-blue shrink-0 w-20">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                <div class="qa-label">Agences</div>
            </a>
            <a href="<?php echo e(route('score-integrite.gestion')); ?>" class="qa-card qa-amber shrink-0 w-20">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                <div class="qa-label">Infractions</div>
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('formations.index')); ?>" class="qa-card qa-blue shrink-0 w-20">
                <div class="qa-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                <div class="qa-label">Formations</div>
            </a>
            <a href="<?php echo e(route('regles.index')); ?>" class="qa-card qa-slate shrink-0 w-20">
                <div class="qa-icon"><span class="text-lg">💬</span></div>
                <div class="qa-label">Message agence</div>
            </a>
        </div>
    </div>

    
    <div class="db-fade mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="db-agency-box">
            <div class="db-agency-box-title"><span aria-hidden="true">💬</span> Message de l'agence</div>
            <div class="db-agency-box-content">
                <?php if(!empty($messageAgence)): ?>
                    <div class="font-semibold text-white/90"><?php echo e($messageAgence->titre); ?></div>
                    <div class="muted mt-1"><?php echo e(\Illuminate\Support\Str::limit(trim($messageAgence->contenu), 160)); ?></div>
                <?php else: ?>
                    <div class="muted">Aucun message pour le moment.</div>
                <?php endif; ?>
            </div>
            <div class="db-agency-box-actions">
                <a class="db-mini-link" href="<?php echo e(route('regles.index')); ?>">Voir tous les messages</a>
            </div>
        </div>

        <div class="db-agency-box">
            <div class="db-agency-box-title"><span aria-hidden="true">🎯</span> Campagne TikTok</div>
            <div class="db-agency-box-content">
                <div class="muted">Accède aux contenus et consignes liés aux campagnes TikTok.</div>
            </div>
            <div class="db-agency-box-actions">
                <a class="db-mini-link" href="<?php echo e(route('formations.index', $hasCatalogueTiktok ? ['catalogue' => 'tiktok'] : [])); ?>">Ouvrir</a>
            </div>
        </div>
    </div>

    
    <?php if($equipes->isNotEmpty()): ?>
    <div class="db-fade">
        <p class="db-section-title">Équipes</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            <?php $__currentLoopData = $equipes->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between px-4 py-3 rounded-xl border border-white/07 bg-white/[0.03] hover:bg-white/[0.06] hover:border-white/12 transition-all group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-[#00d4ff]/15 flex items-center justify-center shrink-0">
                        <span class="text-[#00d4ff] text-xs font-bold"><?php echo e(strtoupper(mb_substr($eq->nom, 0, 2))); ?></span>
                    </div>
                    <span class="text-sm font-medium text-white truncate"><?php echo e($eq->nom); ?></span>
                </div>
                <span class="text-xs font-bold text-[#00d4ff] bg-[#00d4ff]/10 px-2 py-1 rounded-lg shrink-0 ml-2"><?php echo e($eq->createurs_count ?? $eq->createurs()->count()); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/dashboard.blade.php ENDPATH**/ ?>