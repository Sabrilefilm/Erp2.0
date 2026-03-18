<?php $__env->startSection('title', 'Rapports de la semaine'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-8">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-amber-500/20 via-orange-500/10 to-rose-500/10 border border-white/10 p-6 md:p-8">
        <h1 class="text-2xl md:text-3xl font-bold text-white">Rapports de la semaine</h1>
        <p class="text-[#94a3b8] text-sm mt-2">Traçabilité de tous les rapports. Objectif : faire évoluer les personnes et leur donner des consignes adaptées (ex. difficulté à trouver des matchs → les orienter vers d'autres agences ou leur manageur). Vous pouvez valider les rapports une fois lus.</p>
    </div>

    <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
    <div class="rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 text-sm px-4 py-3"><?php echo e(session('info')); ?></div>
    <?php endif; ?>

    <div class="ultra-card rounded-xl p-6 border border-white/10">
        <form method="get" action="<?php echo e(route('rapport-vendredi.index')); ?>" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="annee" class="block text-xs font-medium text-[#94a3b8] mb-1">Année</label>
                <select name="annee" id="annee" class="ultra-input px-3 py-2 rounded-lg text-white text-sm">
                    <?php for($y = (int) now()->format('o'); $y >= (int) now()->format('o') - 2; $y--): ?>
                    <option value="<?php echo e($y); ?>" <?php echo e($annee == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="semaine" class="block text-xs font-medium text-[#94a3b8] mb-1">Semaine</label>
                <select name="semaine" id="semaine" class="ultra-input px-3 py-2 rounded-lg text-white text-sm">
                    <?php for($s = 1; $s <= 53; $s++): ?>
                    <option value="<?php echo e($s); ?>" <?php echo e($semaine == $s ? 'selected' : ''); ?>>Semaine <?php echo e($s); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="equipe_id" class="block text-xs font-medium text-[#94a3b8] mb-1">Agence</label>
                <select name="equipe_id" id="equipe_id" class="ultra-input px-3 py-2 rounded-lg text-white text-sm min-w-[180px]">
                    <option value="">Toutes</option>
                    <?php $__currentLoopData = $equipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($eq->id); ?>" <?php echo e(request('equipe_id') == $eq->id ? 'selected' : ''); ?>><?php echo e($eq->nom); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="user_id" class="block text-xs font-medium text-[#94a3b8] mb-1">Personne</label>
                <select name="user_id" id="user_id" class="ultra-input px-3 py-2 rounded-lg text-white text-sm min-w-[200px]">
                    <option value="">Toutes</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?> (<?php echo e($u->getRoleLabel()); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-lg text-sm font-semibold">Filtrer</button>
        </form>
    </div>

    <?php
        $libelleSemaine = \Carbon\Carbon::now()->setISODate($annee, $semaine)->startOfWeek()->format('d/m/Y');
    ?>

    <h2 class="text-lg font-semibold text-white">Semaine du <?php echo e($libelleSemaine); ?> (<?php echo e(count($rapports)); ?> rapport(s))</h2>

    <?php if($rapports->isEmpty()): ?>
    <div class="rounded-2xl border border-white/10 bg-white/5 p-12 text-center">
        <p class="text-[#94a3b8]">Aucun rapport pour cette semaine avec ces filtres.</p>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php $__currentLoopData = $rapports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article class="ultra-card rounded-xl p-6 border <?php echo e($r->isValide() ? 'border-emerald-500/30 bg-emerald-500/5' : 'border-white/10'); ?>">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                <div>
                    <p class="font-semibold text-white"><?php echo e($r->user->name); ?></p>
                    <p class="text-sm text-[#94a3b8]"><?php echo e($r->user->getRoleLabel()); ?><?php echo e($r->user->equipe ? ' · ' . $r->user->equipe->nom : ''); ?></p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <?php if($r->isValide()): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 text-xs font-medium">
                        ✓ Validé le <?php echo e($r->valide_at->translatedFormat('d/m/Y')); ?><?php if($r->validePar): ?> par <?php echo e($r->validePar->name); ?><?php endif; ?>
                    </span>
                    <?php else: ?>
                    <form action="<?php echo e(route('rapport-vendredi.valider', $r)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="annee" value="<?php echo e(request('annee', now()->format('o'))); ?>">
                        <input type="hidden" name="semaine" value="<?php echo e(request('semaine', now()->format('W'))); ?>">
                        <?php if(request('equipe_id')): ?><input type="hidden" name="equipe_id" value="<?php echo e(request('equipe_id')); ?>"><?php endif; ?>
                        <?php if(request('user_id')): ?><input type="hidden" name="user_id" value="<?php echo e(request('user_id')); ?>"><?php endif; ?>
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500/25 hover:bg-amber-500/35 text-amber-300 font-medium text-sm transition-colors">Valider le rapport</button>
                    </form>
                    <?php endif; ?>
                    <p class="text-xs text-[#64748b]">Enregistré le <?php echo e($r->created_at->translatedFormat('d/m/Y H:i')); ?></p>
                </div>
            </div>
            <div class="text-[#b0bee3] whitespace-pre-wrap text-sm"><?php echo e($r->contenu); ?></div>
        </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/rapport-vendredi/index-fondateur.blade.php ENDPATH**/ ?>