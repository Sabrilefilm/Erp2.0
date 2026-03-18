<?php $__env->startSection('title', 'Corriger heures et jours'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl space-y-6 pb-8">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-amber-500/20 via-orange-500/10 to-rose-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Corriger heures et jours</h1>
                <p class="text-[#94a3b8] text-sm mt-1">En cas d'erreur dans l'Excel, modifiez ici les heures, jours et diamants des créateurs (mois en cours).</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('import.export-donnees', request()->only('equipe_id'))); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l4-4m-4 4V4"/></svg>
                    Exporter les données (Excel)
                </a>
                <a href="<?php echo e(route('import.index')); ?>" class="inline-flex items-center gap-2 text-white/90 hover:text-white text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Retour à l'import Excel
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
    <div class="rounded-xl bg-red-500/20 border border-red-500/40 text-red-400 text-sm px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($e); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="ultra-card rounded-xl p-4 border border-white/10">
        <form method="get" action="<?php echo e(route('import.corriger-heures-jours')); ?>" class="flex flex-wrap items-end gap-4">
            <?php if(auth()->user()->isFondateurPrincipal() && $equipes->isNotEmpty()): ?>
            <div>
                <label for="equipe_id" class="block text-xs font-medium text-[#94a3b8] mb-1">Agence</label>
                <select name="equipe_id" id="equipe_id" class="ultra-input px-3 py-2 rounded-lg text-white text-sm min-w-[200px]" onchange="this.form.submit()">
                    <option value="">Toutes les agences</option>
                    <?php $__currentLoopData = $equipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($eq->id); ?>" <?php echo e($equipeFilter === $eq->id ? 'selected' : ''); ?>><?php echo e($eq->nom); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div>
                <label for="filtre" class="block text-xs font-medium text-[#94a3b8] mb-1">Afficher</label>
                <select name="filtre" id="filtre" class="ultra-input px-3 py-2 rounded-lg text-white text-sm min-w-[200px]" onchange="this.form.submit()">
                    <option value="tous" <?php echo e(($filtre ?? 'tous') === 'tous' ? 'selected' : ''); ?>>Tous les créateurs</option>
                    <option value="objectif_atteint" <?php echo e(($filtre ?? '') === 'objectif_atteint' ? 'selected' : ''); ?>>Objectif atteint (<?php echo e($objectifJours ?? 7); ?>j, <?php echo e($objectifHeures ?? 16); ?>h, <?php echo e(number_format($objectifDiamants ?? 1000, 0, ',', ' ')); ?> diamants)</option>
                    <option value="a_completer" <?php echo e(($filtre ?? '') === 'a_completer' ? 'selected' : ''); ?>>À compléter</option>
                </select>
            </div>
        </form>
        <p class="text-xs text-[#64748b] mt-2">Tri : objectif atteint en haut, puis par heures/jours/diamants (le moins en bas).</p>
    </div>

    <form action="<?php echo e(route('import.mettre-a-jour-heures-jours')); ?>" method="post" class="space-y-4">
        <?php echo csrf_field(); ?>
        <?php if($equipeFilter): ?>
        <input type="hidden" name="equipe_id" value="<?php echo e($equipeFilter); ?>">
        <?php endif; ?>
        <input type="hidden" name="filtre" value="<?php echo e($filtre ?? 'tous'); ?>">

        <div class="rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/5">
                            <th class="text-left px-5 py-3 font-semibold text-white">Créateur</th>
                            <th class="text-right px-5 py-3 font-semibold text-[#94a3b8] w-28">Heures</th>
                            <th class="text-right px-5 py-3 font-semibold text-[#94a3b8] w-24">Jours</th>
                            <th class="text-right px-5 py-3 font-semibold text-[#94a3b8] w-28">Diamants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $createurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3">
                                <span class="font-semibold text-white"><?php echo e($c->nom ?: ($c->user?->name ?? '—')); ?></span>
                                <?php if($c->user?->username): ?>
                                <span class="block text-xs text-[#94a3b8] mt-0.5"><?php echo e($c->user->username); ?></span>
                                <?php endif; ?>
                                <?php if($c->equipe): ?>
                                <span class="block text-xs text-[#64748b] mt-0.5"><?php echo e($c->equipe->nom); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-2 text-right">
                                <input type="text" name="createurs[<?php echo e($c->id); ?>][heures_mois]" value="<?php echo e(old('createurs.'.$c->id.'.heures_mois', $c->heures_mois !== null && $c->heures_mois !== '' ? \App\Support\HeuresHelper::format((float) $c->heures_mois) : '0h00')); ?>" placeholder="ex. 7h30" class="ultra-input w-24 px-2 py-1.5 rounded-lg text-white text-right text-sm">
                            </td>
                            <td class="px-5 py-2 text-right">
                                <input type="number" name="createurs[<?php echo e($c->id); ?>][jours_mois]" value="<?php echo e(old('createurs.'.$c->id.'.jours_mois', $c->jours_mois !== null ? (int) $c->jours_mois : '0')); ?>" min="0" max="31" placeholder="—" class="ultra-input w-16 px-2 py-1.5 rounded-lg text-white text-right text-sm">
                            </td>
                            <td class="px-5 py-2 text-right">
                                <input type="number" name="createurs[<?php echo e($c->id); ?>][diamants]" value="<?php echo e(old('createurs.'.$c->id.'.diamants', $c->diamants !== null ? (int) $c->diamants : '0')); ?>" min="0" placeholder="—" class="ultra-input w-24 px-2 py-1.5 rounded-lg text-white text-right text-sm">
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-[#94a3b8]">Aucun créateur à afficher. <?php if(auth()->user()->isFondateurPrincipal()): ?> Choisissez une agence ou importez des créateurs. <?php endif; ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($createurs->isNotEmpty()): ?>
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="ultra-btn-primary px-5 py-2.5 rounded-xl font-semibold text-sm">Enregistrer les modifications</button>
            <span class="text-xs text-[#94a3b8]">Heures au format 7h30 ou 7.5 (max 744 h). Jours max 31. Laisser vide pour effacer.</span>
        </div>
        <?php endif; ?>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/import/corriger-heures-jours.blade.php ENDPATH**/ ?>