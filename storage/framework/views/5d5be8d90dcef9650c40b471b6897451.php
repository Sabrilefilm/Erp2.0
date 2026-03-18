<?php $__env->startSection('title', 'Centre de formation'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Cards ─────────────────────────────────── */
.explore-card {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    text-decoration: none;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    opacity: 0;
    transform: translateY(12px);
    animation: card-in .4s ease forwards;
}
.explore-card:hover { transform:translateY(-4px)!important; box-shadow:0 16px 40px rgba(0,0,0,.5); border-color:rgba(255,255,255,.2); }
.explore-card:nth-child(1){animation-delay:.03s} .explore-card:nth-child(2){animation-delay:.08s}
.explore-card:nth-child(3){animation-delay:.13s} .explore-card:nth-child(4){animation-delay:.18s}
.explore-card:nth-child(5){animation-delay:.23s} .explore-card:nth-child(6){animation-delay:.28s}
.explore-card:nth-child(7){animation-delay:.33s} .explore-card:nth-child(n+8){animation-delay:.38s}
@keyframes card-in { to { opacity:1; transform:translateY(0); } }
.explore-card-banner { position:relative; height:156px; overflow:hidden; flex-shrink:0; }
.explore-card-banner img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.45; transition:opacity .3s,transform .4s; }
.explore-card:hover .explore-card-banner img { opacity:.65; transform:scale(1.06); }
.explore-card-banner-content { position:absolute; inset:0; padding:14px 16px; display:flex; flex-direction:column; justify-content:space-between; }
.explore-card-category { font-size:10px; font-weight:700; color:rgba(255,255,255,.8); text-transform:uppercase; letter-spacing:.07em; }
.explore-card-title { font-size:16px; font-weight:700; color:#fff; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; text-shadow:0 2px 8px rgba(0,0,0,.6); }
.explore-play-btn { position:absolute; bottom:14px; right:14px; width:42px; height:42px; border-radius:50%; background:rgba(255,255,255,.2); border:2px solid rgba(255,255,255,.5); display:flex; align-items:center; justify-content:center; transition:all .2s; backdrop-filter:blur(6px); }
.explore-card:hover .explore-play-btn { background:rgba(255,255,255,.35); transform:scale(1.12); }
.explore-play-btn svg { width:17px; height:17px; color:#fff; margin-left:2px; }
.explore-card-footer { padding:12px 16px 10px; display:flex; align-items:center; justify-content:space-between; }
.explore-stat { display:flex; flex-direction:column; align-items:center; flex:1; }
.explore-stat-value { font-size:15px; font-weight:600; color:#fff; line-height:1; }
.explore-stat-label { font-size:10px; color:rgba(255,255,255,.3); margin-top:3px; text-transform:uppercase; letter-spacing:.04em; }
.explore-stat-divider { width:1px; height:24px; background:rgba(255,255,255,.1); flex-shrink:0; }
.card-agent-row { padding:7px 14px 9px; display:flex; align-items:center; gap:8px; border-top:1px solid rgba(255,255,255,.05); }

/* ── Filtres ─────────────────────────────── */
.filter-bar { background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:16px 20px; margin-bottom:28px; }
.filter-label { font-size:10px; font-weight:700; color:rgba(255,255,255,.35); text-transform:uppercase; letter-spacing:.1em; margin-bottom:10px; }
.filter-row { display:flex; align-items:center; flex-wrap:wrap; gap:8px; }
.filter-pill { display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:500; text-decoration:none; border:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.5); background:transparent; transition:all .18s; opacity:0; transform:translateY(5px); animation:pill-in .3s ease forwards; }
.filter-pill:hover { background:rgba(255,255,255,.08); color:rgba(255,255,255,.85); border-color:rgba(255,255,255,.2); }
.filter-pill.active { background:#00d4ff; color:#0a0e27; border-color:#00d4ff; font-weight:700; }
.filter-pill:nth-child(1){animation-delay:.00s} .filter-pill:nth-child(2){animation-delay:.04s} .filter-pill:nth-child(3){animation-delay:.08s}
.filter-pill:nth-child(4){animation-delay:.12s} .filter-pill:nth-child(5){animation-delay:.16s} .filter-pill:nth-child(6){animation-delay:.20s}
.filter-pill:nth-child(7){animation-delay:.24s} .filter-pill:nth-child(8){animation-delay:.28s}
@keyframes pill-in { to { opacity:1; transform:translateY(0); } }
.filter-sep { width:1px; height:20px; background:rgba(255,255,255,.12); margin:0 4px; }
.search-wrap { display:flex; align-items:center; gap:8px; margin-top:14px; padding-top:14px; border-top:1px solid rgba(255,255,255,.06); }
.search-input { flex:1; padding:8px 14px; border-radius:10px; border:1px solid rgba(255,255,255,.1); background:rgba(255,255,255,.05); color:#fff; font-size:13px; outline:none; transition:border-color .2s,background .2s; }
.search-input::placeholder { color:rgba(255,255,255,.3); }
.search-input:focus { border-color:rgba(0,212,255,.5); background:rgba(255,255,255,.08); }
.search-btn { padding:8px 18px; border-radius:10px; background:rgba(0,212,255,.15); border:1px solid rgba(0,212,255,.3); color:#00d4ff; font-size:13px; font-weight:600; cursor:pointer; transition:background .18s; white-space:nowrap; }
.search-btn:hover { background:rgba(0,212,255,.25); }
.formation-grid { display:grid; grid-template-columns:repeat(1,1fr); gap:18px; }
@media(min-width:640px){ .formation-grid{ grid-template-columns:repeat(2,1fr); } }
@media(min-width:900px){ .formation-grid{ grid-template-columns:repeat(3,1fr); } }
@media(min-width:1200px){ .formation-grid{ grid-template-columns:repeat(4,1fr); } }

/* ── Modale design ──────────────────────── */
.fmodal-backdrop {
    position: fixed; inset: 0; z-index: 9000;
    background: rgba(5,8,30,0.88);
    backdrop-filter: blur(10px);
    display: flex; align-items: center; justify-content: center;
    padding: 16px;
}
.fmodal {
    background: linear-gradient(160deg, #0d1530 0%, #0f172a 60%, #111827 100%);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 24px;
    width: 100%; max-width: 820px;
    max-height: 92vh; overflow-y: auto;
    display: flex; flex-direction: column;
    box-shadow: 0 32px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.05);
    animation: fmodal-in .28s cubic-bezier(.22,.68,0,1.2);
    scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.1) transparent;
}
@keyframes fmodal-in {
    from { opacity:0; transform:scale(.93) translateY(20px); }
    to   { opacity:1; transform:none; }
}

/* Header de la modale avec bandeau dégradé */
.fmodal-hero {
    position: relative;
    min-height: 200px;
    border-radius: 22px 22px 0 0;
    overflow: hidden;
    flex-shrink: 0;
}
.fmodal-hero-img {
    position: absolute; inset: 0;
    width: 100%; height: 100%; object-fit: cover;
    opacity: 0.35;
}
.fmodal-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.2) 0%, rgba(0,0,0,.65) 100%);
}
.fmodal-hero-content {
    position: relative; z-index: 2;
    padding: 22px 24px 20px;
    display: flex; flex-direction: column; justify-content: space-between;
    min-height: 200px;
}
.fmodal-close {
    position: absolute; top: 16px; right: 16px; z-index: 10;
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(0,0,0,.45); border: 1px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: rgba(255,255,255,.8);
    transition: background .18s, color .18s, transform .18s;
    backdrop-filter: blur(8px);
}
.fmodal-close:hover { background:rgba(255,255,255,.15); color:#fff; transform:scale(1.08); }

/* Zone lecteur / placeholder */
.fmodal-player {
    position: relative;
    background: #000;
    aspect-ratio: 16/9;
}
.fmodal-player iframe { position:absolute; inset:0; width:100%; height:100%; border:0; }
/* Placeholder no-video */
.fmodal-placeholder {
    aspect-ratio: 16/9;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 14px;
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.fmodal-placeholder-icon {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid rgba(255,255,255,.15);
}
.fmodal-placeholder-text { font-size: 14px; color: rgba(255,255,255,.45); font-weight: 500; }

/* Corps modale */
.fmodal-body { padding: 22px 24px; flex: 1; display: flex; flex-direction: column; gap: 18px; }
.fmodal-description {
    font-size: 14px; line-height: 1.75; color: rgba(176,190,227,.85);
    background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06);
    border-radius: 12px; padding: 16px 18px;
}
.fmodal-description ul { padding-left: 0; list-style: none; display: flex; flex-direction: column; gap: 8px; }
.fmodal-description ul li { display:flex; gap:10px; align-items:flex-start; }
.fmodal-description ul li::before { content:''; width:5px; height:5px; border-radius:50%; background:rgba(255,255,255,.35); flex-shrink:0; margin-top:7px; }

/* Boutons actions */
.fmodal-btn-primary {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 13px 20px; border-radius: 14px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    text-decoration: none; transition: all .2s; border: none;
}
.fmodal-btn-quiz {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #fff;
    box-shadow: 0 4px 20px rgba(124,58,237,.35);
}
.fmodal-btn-quiz:hover { box-shadow:0 6px 28px rgba(124,58,237,.5); transform:translateY(-1px); }
.fmodal-btn-content {
    background: rgba(255,255,255,.07);
    color: rgba(255,255,255,.8);
    border: 1px solid rgba(255,255,255,.1);
}
.fmodal-btn-content:hover { background:rgba(255,255,255,.12); color:#fff; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-2 sm:px-4 pt-6 pb-12" x-data="formationModal()" @keydown.escape.window="closeModal()">

    
    <template x-if="open">
        <div class="fmodal-backdrop" @click.self="closeModal()">
            <div class="fmodal">

                
                <div class="fmodal-hero" :style="'background:'+current.grad">
                    <template x-if="current.thumb">
                        <img :src="current.thumb" class="fmodal-hero-img" :alt="current.titre" onerror="this.style.display='none'">
                    </template>
                    <div class="fmodal-hero-overlay"></div>
                    <div class="fmodal-hero-content">
                        <div class="flex items-start justify-between gap-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest bg-white/15 text-white/90 px-3 py-1.5 rounded-full backdrop-blur-sm" x-text="current.catalogue || current.typeLabel"></span>
                            <button @click="closeModal()" class="fmodal-close" type="button">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-xl md:text-2xl leading-snug" x-text="current.titre"></h2>
                            <div class="flex items-center gap-2 mt-3 flex-wrap">
                                <span class="text-[11px] font-semibold text-white/70 bg-white/10 backdrop-blur px-3 py-1 rounded-full" x-text="current.typeLabel"></span>
                                <span class="text-[11px] font-semibold text-green-300 bg-green-500/20 px-3 py-1 rounded-full">Gratuit</span>
                                <template x-if="current.hasQuiz">
                                    <span class="text-[11px] font-semibold text-purple-300 bg-purple-500/20 px-3 py-1 rounded-full">Quiz disponible</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                
                <template x-if="current.youtubeId">
                    <div class="fmodal-player">
                        <iframe :src="'https://www.youtube.com/embed/'+current.youtubeId+'?rel=0&autoplay=1'" :title="current.titre" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </template>
                <template x-if="!current.youtubeId">
                    <div class="fmodal-placeholder" :style="'background: radial-gradient(ellipse at center, '+current.gradLight+' 0%, #0f172a 70%)'">
                        <div class="fmodal-placeholder-icon" :style="'background:'+current.gradLight">
                            <template x-if="current.type === 'document'">
                                <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </template>
                            <template x-if="current.type === 'lien'">
                                <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </template>
                            <template x-if="current.type === 'video'">
                                <svg width="32" height="32" fill="white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </template>
                        </div>
                        <div class="text-center">
                            <p class="fmodal-placeholder-text">Aucune vidéo disponible pour ce module</p>
                            <p class="text-xs text-white/30 mt-1">Consultez le contenu écrit ci-dessous</p>
                        </div>
                    </div>
                </template>

                
                <div class="fmodal-body">
                    
                    <template x-if="current.description">
                        <div class="fmodal-description">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-white/35 mb-3">Contenu du module</p>
                            <div x-html="current.descriptionHtml"></div>
                        </div>
                    </template>
                    <template x-if="!current.description">
                        <div class="fmodal-description text-center py-2">
                            <p class="text-white/40 text-sm">Aucune description disponible.</p>
                        </div>
                    </template>

                    
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-white/35 mb-3">Actions</p>
                        <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                            <template x-if="current.hasFichier">
                                <a :href="current.fichierUrl" class="fmodal-btn-primary flex-1 min-w-[200px] inline-flex items-center justify-center gap-2" style="background: linear-gradient(135deg,#0ea5e9,#0284c7); color: #fff; border-color: rgba(14,165,233,.4);">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span x-text="'Télécharger le document' + (current.fichierNom ? ' « ' + current.fichierNom + ' »' : '')"></span>
                                </a>
                            </template>
                            <a :href="current.contenuUrl" class="fmodal-btn-primary fmodal-btn-content flex-1 min-w-[200px] inline-flex items-center justify-center gap-2">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Accéder au contenu
                            </a>
                            <template x-if="current.hasQuiz">
                                <a :href="current.quizUrl" class="fmodal-btn-primary fmodal-btn-quiz flex-1 min-w-[200px]">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    Passer le mini quiz
                                </a>
                            </template>
                            <template x-if="current.externalUrl && !current.hasQuiz">
                                <a :href="current.externalUrl" target="_blank" rel="noopener" class="fmodal-btn-primary fmodal-btn-content flex-1 min-w-[200px]">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Ouvrir la ressource
                                </a>
                            </template>
                            <template x-if="current.externalUrl && current.hasQuiz">
                                <a :href="current.externalUrl" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-white/15 text-white/70 hover:text-white hover:border-white/25 text-sm font-medium transition-colors">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Ouvrir la ressource
                                </a>
                            </template>
                        </div>
                        <template x-if="current.isAgent">
                            <div class="mt-3">
                                <a :href="current.editUrl" class="inline-flex items-center gap-2 text-sm text-[#94a3b8] hover:text-white transition-colors">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Modifier le module
                                </a>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </template>

    
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#94a3b8] mb-1">Bienvenue sur</p>
            <h1 class="text-3xl font-bold text-white leading-tight">Centre de formation</h1>
        </div>
        <?php if(auth()->user()->canAddEntries()): ?>
        <a href="<?php echo e(route('formations.create')); ?>" title="Ajouter un module" class="mt-1 w-9 h-9 rounded-xl flex items-center justify-center bg-white/5 hover:bg-white/10 border border-white/10 text-[#94a3b8] hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </a>
        <?php endif; ?>
    </div>

    
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-[#00d4ff]/10 to-[#b794f6]/10 border border-white/10 p-5 flex gap-4 items-start">
        <div class="w-10 h-10 rounded-xl bg-[#00d4ff]/20 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-[#00d4ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-white">Toutes les formations sont gratuites 🎓</p>
            <p class="text-sm text-[#94a3b8] mt-1 leading-relaxed">
                Cliquez sur un module pour l'ouvrir — <strong class="text-white/70">le lecteur s'affiche toujours</strong>, avec la vidéo si disponible, sinon un contenu écrit. Terminez par le mini quiz pour valider vos acquis.
            </p>
        </div>
    </div>

    
    <div class="filter-bar">
        <p class="filter-label">Filtrer</p>
        <div class="filter-row mb-3">
            <a href="<?php echo e(route('formations.index', array_filter(['q' => request('q'), 'catalogue' => request('catalogue')]))); ?>" class="filter-pill <?php echo e(!request('type') ? 'active' : ''); ?>">Tous</a>
            <a href="<?php echo e(route('formations.index', array_filter(['type' => 'video', 'q' => request('q'), 'catalogue' => request('catalogue')]))); ?>" class="filter-pill <?php echo e(request('type') === 'video' ? 'active' : ''); ?>">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>Vidéos
            </a>
            <a href="<?php echo e(route('formations.index', ['type' => 'document', 'q' => request('q'), 'catalogue' => request('catalogue')])); ?>" class="filter-pill <?php echo e(request('type') === 'document' ? 'active' : ''); ?>">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Documents
            </a>
            <a href="<?php echo e(route('formations.index', ['type' => 'lien', 'q' => request('q'), 'catalogue' => request('catalogue')])); ?>" class="filter-pill <?php echo e(request('type') === 'lien' ? 'active' : ''); ?>">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>Liens
            </a>
            <div class="filter-sep"></div>
            <a href="<?php echo e(route('formations.index', array_filter(['q' => request('q'), 'type' => request('type')]))); ?>" class="filter-pill <?php echo e(!request('catalogue') ? 'active' : ''); ?>">Tous les thèmes</a>
            <?php $__currentLoopData = $catalogues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('formations.index', ['catalogue' => $cat->slug, 'q' => request('q'), 'type' => request('type')])); ?>" class="filter-pill <?php echo e(request('catalogue') === $cat->slug ? 'active' : ''); ?>"><?php echo e($cat->label); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <form action="<?php echo e(route('formations.index')); ?>" method="GET" class="search-wrap">
            <?php if(request('type')): ?><input type="hidden" name="type" value="<?php echo e(request('type')); ?>"><?php endif; ?>
            <?php if(request('catalogue')): ?><input type="hidden" name="catalogue" value="<?php echo e(request('catalogue')); ?>"><?php endif; ?>
            <svg class="w-4 h-4 text-[#94a3b8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Rechercher un module…" class="search-input">
            <button type="submit" class="search-btn">Rechercher</button>
            <?php if(request('q') || request('type') || request('catalogue')): ?>
            <a href="<?php echo e(route('formations.index')); ?>" class="text-xs text-[#94a3b8] hover:text-white transition-colors">Réinitialiser</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(session('success')): ?>
    <div class="rounded-xl bg-[#00d4ff]/10 border border-[#00d4ff]/30 text-[#00d4ff] text-sm px-4 py-3 mb-6"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-white">
            <?php if(request('q')): ?>Résultats pour « <?php echo e(request('q')); ?> »
            <?php elseif(request('catalogue')): ?><?php echo e($catalogues->firstWhere('slug', request('catalogue'))?->label ?? request('catalogue')); ?>

            <?php elseif(request('type') && isset(\App\Models\Formation::TYPES[request('type')])): ?><?php echo e(\App\Models\Formation::TYPES[request('type')]); ?>s
            <?php else: ?> À la une <?php endif; ?>
        </h2>
        <?php if(request('catalogue') || request('type') || request('q')): ?>
        <a href="<?php echo e(route('formations.index')); ?>" class="text-sm font-semibold text-[#00d4ff] bg-[#00d4ff]/10 px-4 py-1.5 rounded-lg hover:bg-[#00d4ff]/20 transition-colors">Tout afficher</a>
        <?php endif; ?>
    </div>

    
    <?php if($formations->isEmpty()): ?>
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-12 text-center">
        <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-[#94a3b8] text-sm">Aucune formation dans cette catégorie.</p>
        <?php if(auth()->user()->canAddEntries()): ?>
        <a href="<?php echo e(route('formations.create')); ?>" class="inline-block mt-4 px-4 py-2 rounded-xl bg-[#00d4ff] text-[#0a0e27] text-sm font-semibold hover:bg-[#00d4ff]/90 transition-colors">Ajouter un module</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="formation-grid">
        <?php $__currentLoopData = $formations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $youtubeId   = $f->url ? \App\Models\Formation::youtubeIdFromUrl($f->url) : null;
            $thumb       = $youtubeId ? "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg" : null;
            $gradients   = ['video'=>'linear-gradient(135deg,#b91c1c,#ef4444)','document'=>'linear-gradient(135deg,#1d4ed8,#3b82f6)','lien'=>'linear-gradient(135deg,#065f46,#10b981)'];
            $gradLights  = ['video'=>'rgba(239,68,68,.25)','document'=>'rgba(59,130,246,.25)','lien'=>'rgba(16,185,129,.25)'];
            $grad        = $gradients[$f->type] ?? 'linear-gradient(135deg,#374151,#6b7280)';
            $gradLight   = $gradLights[$f->type] ?? 'rgba(107,114,128,.25)';
            $catalogueLabel = $f->catalogue ? ($catalogues->firstWhere('slug', $f->catalogue)->label ?? null) : null;
            $typeLabel   = \App\Models\Formation::TYPES[$f->type] ?? $f->type;
            $contentLabel = $catalogueLabel ?: ($f->type==='video' ? 'Cours vidéo' : ($f->type==='document' ? 'Formation écrite' : 'Ressource externe'));
            $isAgent = auth()->user()->canAddEntries();
            // Description en HTML pour la modale
            $lines       = $f->description ? array_values(array_filter(preg_split('/\r\n|\r|\n/', $f->description))) : [];
            $descHtml    = count($lines) ? '<ul>'.implode('', array_map(fn($l) => '<li>'.e($l).'</li>', $lines)).'</ul>' : '';
        ?>
        <div class="explore-card <?php echo e($f->actif ? '' : 'opacity-60'); ?>"
             @click="openModal({
                titre:        <?php echo e(Js::from($f->titre)); ?>,
                description:  <?php echo e(Js::from($f->description)); ?>,
                descriptionHtml: <?php echo e(Js::from($descHtml)); ?>,
                catalogue:    <?php echo e(Js::from($catalogueLabel)); ?>,
                typeLabel:    <?php echo e(Js::from($contentLabel)); ?>,
                type:         <?php echo e(Js::from($f->type)); ?>,
                youtubeId:    <?php echo e(Js::from($youtubeId)); ?>,
                thumb:        <?php echo e(Js::from($thumb)); ?>,
                grad:         <?php echo e(Js::from($grad)); ?>,
                gradLight:    <?php echo e(Js::from($gradLight)); ?>,
                hasQuiz:      <?php echo e($f->questions_count > 0 ? 'true' : 'false'); ?>,
                quizUrl:      '<?php echo e(route('formations.quiz.show', $f)); ?>',
                externalUrl:  <?php echo e(Js::from($f->type !== 'video' && $f->url ? $f->url : null)); ?>,
                hasFichier:   <?php echo e($f->fichier_path ? 'true' : 'false'); ?>,
                fichierUrl:   <?php echo e(Js::from($f->fichier_path ? route('formations.fichier', $f) : null)); ?>,
                fichierNom:   <?php echo e(Js::from($f->fichier_nom ?? '')); ?>,
                contenuUrl:   '<?php echo e(route('formations.contenu', $f)); ?>',
                isAgent:      <?php echo e($isAgent ? 'true' : 'false'); ?>,
                editUrl:      '<?php echo e(route('formations.edit', $f)); ?>'
             })">

            <div class="explore-card-banner" style="background:<?php echo e($grad); ?>;">
                <?php if($thumb): ?>
                <img src="<?php echo e($thumb); ?>" alt="" loading="lazy" onerror="this.style.display='none'">
                <?php endif; ?>
                <div class="explore-card-banner-content">
                    <p class="explore-card-category"><?php echo e($contentLabel); ?></p>
                    <p class="explore-card-title"><?php echo e($f->titre); ?></p>
                </div>
                <div class="explore-play-btn">
                    <?php if($f->type==='video'): ?><svg fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <?php elseif($f->type==='document'): ?><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <?php else: ?><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg><?php endif; ?>
                </div>
            </div>
            <div class="explore-card-footer">
                <div class="explore-stat">
                    <span class="explore-stat-value" style="font-size:13px;"><?php echo e($f->type==='video'?'🎬':($f->type==='document'?'📄':'🔗')); ?></span>
                    <span class="explore-stat-label"><?php echo e($typeLabel); ?></span>
                </div>
                <div class="explore-stat-divider"></div>
                <div class="explore-stat">
                    <span class="explore-stat-value" style="font-size:12px;"><?php echo e($f->questions_count > 0 ? '✅' : '—'); ?></span>
                    <span class="explore-stat-label">Quiz</span>
                </div>
                <div class="explore-stat-divider"></div>
                <div class="explore-stat">
                    <span class="explore-stat-value" style="font-size:11px;font-weight:700;color:#00d4ff;">Gratuit</span>
                    <span class="explore-stat-label">Accès</span>
                </div>
            </div>
            <?php if($isAgent): ?>
            <div class="card-agent-row" @click.stop="">
                <a href="<?php echo e(route('formations.edit', $f)); ?>" class="text-xs text-[#94a3b8] hover:text-white flex items-center gap-1 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Modifier
                </a>
                <form action="<?php echo e(route('formations.destroy', $f)); ?>" method="POST" class="inline ml-auto" onsubmit="return confirm('Supprimer ce module ?');">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="text-xs text-[#94a3b8] hover:text-red-400 flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Supprimer
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>

<script>
function formationModal() {
    return {
        open: false,
        current: {},
        openModal(data) {
            this.current = data;
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.open = false;
            document.body.style.overflow = '';
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/formations/index.blade.php ENDPATH**/ ?>