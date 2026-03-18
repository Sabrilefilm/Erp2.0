<?php $__env->startSection('title', 'Données match — Répertoire'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-8 max-w-4xl">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-cyan-600/20 to-indigo-600/20 border border-cyan-500/25 p-5">
        <h1 class="text-xl font-bold text-white">Données match (répertoire)</h1>
        <p class="text-[#94a3b8] text-sm mt-1">Répertoire des créateurs adverses : @ TikTok, nom, téléphone, agent, agence… Ces données pré-remplissent le formulaire lors de la création d'un match.</p>
    </div>

    <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="ultra-card rounded-xl p-5 border border-white/10">
        <h2 class="text-sm font-semibold text-white mb-4">Ajouter un contact adverse</h2>
        <form action="<?php echo e(route('donnees-match.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">@ TikTok *</label>
                    <input type="text" name="tiktok_at" value="<?php echo e(old('tiktok_at')); ?>" required placeholder="username (sans @)" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
                    <?php $__errorArgs = ['tiktok_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Téléphone *</label>
                    <input type="text" name="telephone" value="<?php echo e(old('telephone')); ?>" required placeholder="06 12 34 56 78" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
                    <?php $__errorArgs = ['telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Agent / e</label>
                    <input type="text" name="agent" value="<?php echo e(old('agent')); ?>" placeholder="Nom de l'agent" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Agence</label>
                    <input type="text" name="agence" value="<?php echo e(old('agence')); ?>" placeholder="Agence du créateur adverse" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="email@exemple.com" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#b0bee3] mb-1">Autres infos</label>
                    <input type="text" name="autres_infos" value="<?php echo e(old('autres_infos')); ?>" placeholder="Contact, notes…" class="ultra-input w-full px-3 py-2 rounded-xl text-white text-sm border border-white/10">
                </div>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold transition-colors">Ajouter</button>
        </form>
    </div>

    <div class="ultra-card rounded-xl overflow-hidden border border-white/10">
        <div class="px-4 py-3 border-b border-white/10 flex flex-wrap items-center gap-3">
            <h2 class="text-sm font-semibold text-white">Répertoire (<?php echo e($totalCount ?? $adverses->count()); ?>)</h2>
            <form action="<?php echo e(route('donnees-match.index')); ?>" method="GET" class="flex-1 min-w-[200px] flex items-center gap-2">
                <input type="search" name="q" value="<?php echo e($searchQuery ?? ''); ?>" placeholder="Rechercher par @, nom, agence, agent, tél…" class="ultra-input flex-1 min-w-0 px-3 py-2 rounded-xl text-white text-sm border border-white/10 placeholder-[#64748b] focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
                <button type="submit" class="px-3 py-2 rounded-xl bg-cyan-500/20 border border-cyan-400/40 text-cyan-300 text-sm font-medium hover:bg-cyan-500/30 transition-colors shrink-0">Rechercher</button>
                <?php if($searchQuery ?? ''): ?>
                <a href="<?php echo e(route('donnees-match.index')); ?>" class="px-2 py-2 text-[#94a3b8] hover:text-white text-sm shrink-0">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white/5 text-left border-b border-white/10">
                        <th class="p-3 font-medium text-[#94a3b8]">Agence</th>
                        <th class="p-3 font-medium text-[#94a3b8]">@ TikTok</th>
                        <th class="p-3 font-medium text-[#94a3b8]">Téléphone</th>
                        <th class="p-3 font-medium text-[#94a3b8]">Agent</th>
                        <th class="p-3 font-medium text-[#94a3b8] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $adverses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $tel = $a->telephone ?? '';
                        $telMasked = strlen($tel) > 4 ? substr($tel, 0, 2) . ' ** ** ** ' . substr($tel, -2) : (strlen($tel) > 0 ? '** ** **' : '—');
                    ?>
                    <tr class="border-b border-white/5 hover:bg-white/[0.03]">
                        <td class="p-3 text-amber-200/90 font-medium"><?php echo e($a->agence ?? '—'); ?></td>
                        <td class="p-3 text-cyan-300 font-medium"><?php echo e('@' . $a->tiktok_at); ?></td>
                        <td class="p-3 text-white/80">
                            <span class="tel-masked"><?php echo e($telMasked); ?></span>
                            <span class="tel-full hidden" data-tel="<?php echo e($tel); ?>"><?php echo e($tel); ?></span>
                            <?php if(strlen($tel) > 0): ?>
                            <button type="button" class="ml-2 px-2 py-0.5 rounded text-xs font-medium bg-white/10 text-cyan-300 hover:bg-white/20 toggle-tel" aria-label="Afficher le numéro">Voir</button>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-white/70"><?php echo e($a->agent ?? '—'); ?></td>
                        <td class="p-3 text-right">
                            <a href="<?php echo e(route('donnees-match.edit', $a->id)); ?>" class="px-2 py-1 rounded-lg bg-white/10 text-white/90 hover:bg-white/20 text-xs font-medium transition-colors inline-block mr-1">Modifier</a>
                            <form action="<?php echo e(route('donnees-match.destroy', $a->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Supprimer ce contact du répertoire ?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="px-2 py-1 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 text-xs font-medium transition-colors">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-[#94a3b8]"><?php if($searchQuery ?? ''): ?>Aucun résultat pour « <?php echo e($searchQuery); ?> ».<?php else: ?> Aucun contact. Ajoutez-en un ci-dessus.<?php endif; ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    document.querySelectorAll('.toggle-tel').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var cell = this.closest('td');
            var masked = cell.querySelector('.tel-masked');
            var full = cell.querySelector('.tel-full');
            if (!masked || !full) return;
            if (full.classList.contains('hidden')) {
                full.classList.remove('hidden');
                masked.classList.add('hidden');
                btn.textContent = 'Masquer';
                btn.setAttribute('aria-label', 'Masquer le numéro');
            } else {
                full.classList.add('hidden');
                masked.classList.remove('hidden');
                btn.textContent = 'Voir';
                btn.setAttribute('aria-label', 'Afficher le numéro');
            }
        });
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/donnees-match/index.blade.php ENDPATH**/ ?>