<?php $__env->startSection('title', 'Annonces & Campagnes TikTok'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Style moderne pour les annonces */
.annonce-card {
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.08);
    background: linear-gradient(165deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100%);
    overflow: hidden;
    transition: all 0.3s ease;
}
.annonce-card:hover {
    border-color: rgba(255,255,255,0.12);
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    transform: translateY(-2px);
}
.annonce-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.annonce-type-annonce {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}
.annonce-type-evenement {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}
.annonce-type-campagne {
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: white;
}
.annonce-details {
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
    padding: 1rem;
    margin-top: 1rem;
}
.annonce-detail-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}
.annonce-detail-item:last-child {
    margin-bottom: 0;
}
.annonce-detail-icon {
    width: 1.25rem;
    height: 1.25rem;
    opacity: 0.7;
}
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.filter-tab {
    padding: 0.625rem 1.25rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.02);
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}
.filter-tab:hover {
    background: rgba(255,255,255,0.05);
    color: white;
}
.filter-tab.active {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border-color: transparent;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-8 max-w-4xl mx-auto">
    
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-500/25 via-violet-500/15 to-purple-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-3xl">📢</div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Annonces & Campagnes TikTok</h1>
                    <p class="text-[#94a3b8] text-sm mt-1">Événements, campagnes et annonces de l'agence 🚀</p>
                </div>
            </div>
            <?php if(auth()->user()->canAddEntries()): ?>
            <a href="<?php echo e(route('annonces.create')); ?>" class="ultra-btn-cta shrink-0"><span>+ Nouvelle annonce</span></a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="filter-tabs">
        <a href="<?php echo e(route('annonces.index', ['type' => 'all'])); ?>" class="filter-tab <?php echo e($type === 'all' ? 'active' : ''); ?>">
            📋 Tout voir
        </a>
        <a href="<?php echo e(route('annonces.index', ['type' => 'annonce'])); ?>" class="filter-tab <?php echo e($type === 'annonce' ? 'active' : ''); ?>">
            📢 Annonces
        </a>
        <a href="<?php echo e(route('annonces.index', ['type' => 'evenement'])); ?>" class="filter-tab <?php echo e($type === 'evenement' ? 'active' : ''); ?>">
            🎉 Événements
        </a>
        <a href="<?php echo e(route('annonces.index', ['type' => 'campagne'])); ?>" class="filter-tab <?php echo e($type === 'campagne' ? 'active' : ''); ?>">
            🎯 Campagnes
        </a>
    </div>

    <?php if($annonces->isEmpty()): ?>
    <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center">
        <div class="w-20 h-20 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4 text-4xl">📭</div>
        <p class="text-[#94a3b8] text-lg">Aucune annonce pour le moment.</p>
        <p class="text-[#64748b] text-sm mt-1">L'agence pourra poster des annonces et campagnes ici.</p>
        <?php if(auth()->user()->canAddEntries()): ?>
        <a href="<?php echo e(route('annonces.create')); ?>" class="inline-block mt-6 px-5 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white font-semibold text-sm">+ Nouvelle annonce</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="space-y-6">
        <?php $__currentLoopData = $annonces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $annonce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article class="annonce-card p-6 md:p-8 <?php echo e($annonce->actif ? '' : 'opacity-60'); ?>">
            <header class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="annonce-type-badge annonce-type-<?php echo e($annonce->type); ?>">
                            <?php if($annonce->type === 'annonce'): ?>📢<?php elseif($annonce->type === 'evenement'): ?>🎉<?php else: ?>🎯<?php endif; ?>
                            <?php echo e($annonce->type_label); ?>

                        </span>
                        <?php if(!$annonce->actif): ?>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-medium bg-white/10 text-[#94a3b8] border border-white/10">Brouillon</span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-xl md:text-2xl font-bold text-white mb-2"><?php echo e($annonce->titre); ?></h2>
                    <p class="text-[#94a3b8] text-sm">📅 <?php echo e($annonce->updated_at->translatedFormat('d F Y \à H:i')); ?></p>
                </div>
                <?php if(auth()->user()->canAddEntries()): ?>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="<?php echo e(route('annonces.edit', $annonce)); ?>" class="px-3 py-1.5 rounded-lg bg-indigo-500/20 text-indigo-400 hover:bg-indigo-500/30 text-sm font-medium">Modifier</a>
                    <form action="<?php echo e(route('annonces.destroy', $annonce)); ?>" method="POST" class="inline" onsubmit="return confirm('Supprimer cette annonce ?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 text-sm font-medium">Supprimer</button>
                    </form>
                </div>
                <?php endif; ?>
            </header>
            
            <div class="text-white/90 leading-relaxed mb-4">
                <?php echo nl2br(e($annonce->contenu)); ?>

            </div>

            
            <?php if($annonce->type === 'evenement' && ($annonce->date_evenement || $annonce->lieu_evenement)): ?>
            <div class="annonce-details">
                <?php if($annonce->date_evenement): ?>
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-white/80"><?php echo e($annonce->date_evenement->translatedFormat('d F Y \à H:i')); ?></span>
                </div>
                <?php endif; ?>
                <?php if($annonce->lieu_evenement): ?>
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-white/80"><?php echo e($annonce->lieu_evenement); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($annonce->type === 'campagne' && ($annonce->hashtag_principal || $annonce->date_debut || $annonce->date_fin || $annonce->objectif_campagne)): ?>
            <div class="annonce-details">
                <?php if($annonce->hashtag_principal): ?>
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    <span class="text-white/80">#<?php echo e($annonce->hashtag_principal); ?></span>
                </div>
                <?php endif; ?>
                <?php if($annonce->date_debut && $annonce->date_fin): ?>
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-white/80">Du <?php echo e($annonce->date_debut->translatedFormat('d F Y')); ?> au <?php echo e($annonce->date_fin->translatedFormat('d F Y')); ?></span>
                </div>
                <?php endif; ?>
                <?php if($annonce->objectif_campagne): ?>
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-white/80"><?php echo e($annonce->objectif_campagne); ?></span>
                </div>
                <?php endif; ?>
                <?php if($annonce->lien_tiktok): ?>
                <div class="annonce-detail-item">
                    <svg class="annonce-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <a href="<?php echo e($annonce->lien_tiktok); ?>" target="_blank" class="text-indigo-400 hover:text-indigo-300 underline">Voir sur TikTok</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/annonces/index.blade.php ENDPATH**/ ?>