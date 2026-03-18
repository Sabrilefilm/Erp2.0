<?php $__env->startSection('title', 'Liste noire'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <h1 class="text-2xl font-bold bg-gradient-to-r from-neon-blue to-neon-purple bg-clip-text text-transparent">Utilisateurs restreints</h1>

    <div class="ultra-card rounded-xl p-4 border border-white/10">
        <h2 class="text-lg font-semibold text-white mb-3">Ajouter une entrée</h2>
        <form action="<?php echo e(route('blacklist.store')); ?>" method="POST" class="flex flex-wrap items-end gap-3">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Identifiant / Pseudo *</label>
                <input type="text" name="username" required class="ultra-input px-3 py-2 rounded-xl text-white text-sm min-w-[160px]" placeholder="username">
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Prénom</label>
                <input type="text" name="first_name" class="ultra-input px-3 py-2 rounded-xl text-white text-sm" placeholder="Optionnel">
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Nom</label>
                <input type="text" name="last_name" class="ultra-input px-3 py-2 rounded-xl text-white text-sm" placeholder="Optionnel">
            </div>
            <div>
                <label class="block text-sm text-[#b0bee3] mb-1">Téléphone</label>
                <input type="tel" name="phone" class="ultra-input px-3 py-2 rounded-xl text-white text-sm" placeholder="Optionnel (ex. 06 12 34 56 78)">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-sm text-[#b0bee3] mb-1">Raison</label>
                <input type="text" name="raison" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm" placeholder="Optionnel">
            </div>
            <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-xl text-sm font-semibold"><span>Ajouter</span></button>
        </form>
    </div>

    <div class="ultra-card rounded-xl overflow-hidden border border-white/10">
        <form action="<?php echo e(route('blacklist.index')); ?>" method="GET" class="p-4 border-b border-white/10">
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Rechercher (pseudo, téléphone, nom)…" class="ultra-input w-full max-w-md px-4 py-2.5 rounded-xl text-white placeholder-[#6b7a9f]">
            <button type="submit" class="ultra-btn-primary mt-2 px-4 py-2 rounded-xl text-sm font-semibold"><span>Rechercher</span></button>
        </form>
        <table class="ultra-table w-full">
            <thead>
                <tr class="bg-neon-blue/10">
                    <th class="text-left p-4 text-white font-semibold text-sm">Pseudo</th>
                    <th class="text-left p-4 text-white font-semibold text-sm">Nom</th>
                    <th class="text-left p-4 text-white font-semibold text-sm">Téléphone</th>
                    <th class="text-left p-4 text-white font-semibold text-sm">Raison</th>
                    <th class="text-left p-4 text-white font-semibold text-sm">Ajouté le</th>
                    <th class="p-4 text-white font-semibold text-sm text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $blacklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="p-4 text-[#b0bee3] font-medium"><?php echo e($b->username); ?></td>
                    <td class="p-4 text-[#b0bee3]"><?php echo e(trim($b->first_name . ' ' . $b->last_name) ?: '—'); ?></td>
                    <td class="p-4 text-[#b0bee3]"><?php echo e($b->phone ?? $b->email ?? '—'); ?></td>
                    <td class="p-4 text-[#b0bee3]"><?php echo e($b->raison ?? '—'); ?></td>
                    <td class="p-4 text-[#6b7a9f] text-sm"><?php echo e($b->created_at->format('d/m/Y')); ?></td>
                    <td class="p-4 text-right">
                        <form action="<?php echo e(route('blacklist.destroy', $b)); ?>" method="POST" class="inline" onsubmit="return confirm('Retirer cette entrée de la liste ?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-neon-pink hover:underline text-sm">Retirer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="p-8 text-center text-[#b0bee3]">Aucune entrée.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($blacklist->hasPages()): ?>
        <div class="p-3 border-t border-white/10"><?php echo e($blacklist->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/blacklist/index.blade.php ENDPATH**/ ?>