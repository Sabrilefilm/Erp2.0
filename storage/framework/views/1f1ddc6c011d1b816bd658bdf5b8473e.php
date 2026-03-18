<table class="w-full min-w-[500px]">
    <thead>
        <tr class="border-b border-white/10 bg-white/5">
            <th class="text-left px-5 py-3 font-semibold text-white">Nom</th>
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Utilisateur</th>
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Rôle</th>
            <?php if(auth()->user()->isFondateurPrincipal()): ?>
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Agence / Sous-agence</th>
            <?php endif; ?>
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Agent / Manageur</th>
            <?php if(auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur() || auth()->user()->isManageur() || auth()->user()->isSousManager()): ?>
            <th class="text-center px-5 py-3 font-semibold text-[#94a3b8] w-24">Contrat</th>
            <th class="px-5 py-3 font-semibold text-white text-right">Action</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
            <td class="px-5 py-3.5 font-medium text-white"><?php echo e($u->name); ?></td>
            <td class="px-5 py-3.5 text-[#94a3b8]"><?php echo e($u->username ?? '—'); ?></td>
            <td class="px-5 py-3.5 text-[#94a3b8]"><?php echo e($u->getRoleLabel()); ?></td>
            <?php if(auth()->user()->isFondateurPrincipal()): ?>
            <td class="px-5 py-3.5 text-[#94a3b8]"><?php echo e($u->equipe?->nom ?? ($u->createur?->equipe?->nom ?? '—')); ?></td>
            <?php endif; ?>
            <td class="px-5 py-3.5 text-[#94a3b8]">
                <?php if($u->isCreateur()): ?>
                    <?php
                        $agentName = $u->createur?->agent?->name ?? $u->equipe?->agents->first()?->name ?? null;
                        $manageurName = $u->createur?->equipe?->manager?->name ?? $u->equipe?->manager?->name ?? null;
                    ?>
                    <?php if($agentName || $manageurName): ?>
                        <?php if($agentName): ?><span class="block"><span class="text-white/50 text-xs">Agent :</span> <?php echo e($agentName); ?></span><?php endif; ?>
                        <?php if($manageurName): ?><span class="block text-white/60 text-xs"><span class="text-white/40">Mg :</span> <?php echo e($manageurName); ?></span><?php endif; ?>
                    <?php else: ?>
                        <span class="text-white/40">—</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-white/40">—</span>
                <?php endif; ?>
            </td>
            <?php if(auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur() || auth()->user()->isManageur() || auth()->user()->isSousManager()): ?>
            <td class="px-5 py-3.5 text-center">
                <?php if($u->isCreateur() && $u->createur): ?>
                    <?php if($u->createur->contrat_signe_le): ?>
                    <a href="<?php echo e(route('createurs.contrat-pdf', $u->createur)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 font-medium" title="Télécharger le contrat signé">✓ Signé</a>
                    <?php else: ?>
                    <span class="text-red-400" title="Non signé">❌</span>
                    <?php endif; ?>
                <?php else: ?>
                <span class="text-white/30">—</span>
                <?php endif; ?>
            </td>
            <td class="px-5 py-3.5 text-right">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $u)): ?>
                <a href="<?php echo e(route('users.edit', $u)); ?>" class="text-sky-400 hover:text-sky-300 font-medium">Modifier</a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $u)): ?>
                <form action="<?php echo e(route('users.destroy', $u)); ?>" method="POST" class="inline ml-2" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="text-red-400 hover:text-red-300 font-medium">Supprimer</button>
                </form>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <?php
                $nbCols = 4; // Nom, Utilisateur, Rôle, Agent (ou Agence si fondateur)
                if (auth()->user()->isFondateurPrincipal()) { $nbCols++; } // Agence
                if (auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur() || auth()->user()->isManageur() || auth()->user()->isSousManager()) { $nbCols += 2; } // Contrat + Action
            ?>
            <td colspan="<?php echo e($nbCols); ?>" class="px-5 py-12 text-center text-[#94a3b8]">Aucun utilisateur.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php if($users->hasPages()): ?>
<div class="px-5 py-4 border-t border-white/10">
    <?php echo e($users->withPath(route('users.index'))->appends(request()->only('role', 'q'))->links()); ?>

</div>
<?php endif; ?>
<?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/users/partials/table-content.blade.php ENDPATH**/ ?>