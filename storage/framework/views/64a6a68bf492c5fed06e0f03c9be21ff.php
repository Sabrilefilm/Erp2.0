<?php $__env->startSection('title', 'Gestion des infractions — Score d\'intégrité'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-8 max-w-4xl mx-auto">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-amber-500/20 via-orange-500/10 to-red-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-2xl">⚠️</div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Infractions & score d'intégrité</h1>
                    <p class="text-[#94a3b8] text-sm mt-0.5">Choisir un créateur, puis enregistrer une infraction ou modifier son score (0–100).</p>
                </div>
            </div>
            <?php if($createurSelect): ?>
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <span class="text-2xl font-bold text-white tabular-nums"><?php echo e($scoreActuel); ?><span class="text-[#94a3b8] font-normal text-lg">/<?php echo e($scoreMax); ?></span></span>
                <span class="text-xs text-[#94a3b8]">Score actuel · <?php echo e($createurSelect->nom); ?></span>
                <form action="<?php echo e(route('createurs.destroy', $createurSelect)); ?>" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement ce créateur (<?php echo e($createurSelect->nom); ?>) et toutes ses données (score, historique, sanctions, etc.) ? Cette action est irréversible.');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-500/50 text-red-400 hover:bg-red-500/20 transition-colors">Supprimer ce créateur</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3">✓ <?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/5">
        <div class="px-5 py-4 border-b border-white/10">
            <h2 class="text-lg font-bold text-white">Ajouter une infraction ou modifier le score</h2>
            <p class="text-[#94a3b8] text-sm mt-1">Sélectionne le créateur concerné. Le score actuel de ce créateur sera utilisé comme « score avant ».</p>
        </div>
        <form action="<?php echo e(route('score-integrite.store-infraction')); ?>" method="POST" class="p-5 space-y-4" id="form-infraction">
            <?php echo csrf_field(); ?>
            <?php
                $createursSiJson = $createurs->map(fn($c) => ['id' => $c->id, 'nom' => $c->nom, 'pseudo' => $c->pseudo_tiktok ?? ''])->values();
                $initialSiId = old('createur_id', $createurSelect?->id ?? '');
                $initialSiCreateur = $createurs->firstWhere('id', $initialSiId);
            ?>
            <div class="relative" x-data="{
                createurs: <?php echo e(Js::from($createursSiJson)); ?>,
                query: <?php echo e(Js::from($initialSiCreateur ? $initialSiCreateur->nom . ($initialSiCreateur->pseudo_tiktok ? ' (@' . $initialSiCreateur->pseudo_tiktok . ')' : '') : '')); ?>,
                selectedId: <?php echo e(Js::from((string)$initialSiId)); ?>,
                open: false,
                focusedIndex: 0,
                gestionUrl: <?php echo e(Js::from(route('score-integrite.gestion'))); ?>,
                get filtered() {
                    const q = (this.query || '').trim().toLowerCase().replace(/^@|\s*\(@[^)]*\)$/g, '');
                    if (!q) return this.createurs.slice(0, 15);
                    return this.createurs.filter(c => (c.nom && c.nom.toLowerCase().includes(q)) || (c.pseudo && c.pseudo.toLowerCase().includes(q))).slice(0, 20);
                },
                onInput() { this.open = true; this.focusedIndex = 0; },
                focusNext() { const list = this.filtered; if (list.length) this.focusedIndex = (this.focusedIndex + 1) % list.length; },
                focusPrev() { const list = this.filtered; if (list.length) this.focusedIndex = (this.focusedIndex - 1 + list.length) % list.length; },
                selectFocused() { const list = this.filtered; if (list[this.focusedIndex]) this.select(list[this.focusedIndex]); },
                select(c) {
                    this.selectedId = String(c.id);
                    this.query = c.nom + (c.pseudo ? ' (@' + c.pseudo + ')' : '');
                    this.open = false;
                    window.location = this.gestionUrl + '?createur_id=' + c.id;
                }
            }">
                <label for="createur_search" class="block text-sm font-medium text-[#94a3b8] mb-1">Créateur <span class="text-red-400">*</span></label>
                <input type="text"
                       id="createur_search"
                       x-model="query"
                       @input="onInput()"
                       @focus="if(query) open = true"
                       @keydown.arrow-down.prevent="focusNext()"
                       @keydown.arrow-up.prevent="focusPrev()"
                       @keydown.enter.prevent="selectFocused()"
                       placeholder="Tapez pour rechercher un créateur…"
                       autocomplete="off"
                       class="ultra-input w-full px-4 py-3 rounded-xl text-white text-sm border border-white/10 bg-white/5">
                <input type="hidden" name="createur_id" :value="selectedId">
                <div x-show="open && filtered.length > 0"
                     x-transition
                     class="absolute left-0 right-0 mt-1 max-h-56 overflow-y-auto rounded-xl border-2 border-white/20 shadow-xl z-20"
                     style="background-color: #1e293b;"
                     @click.outside="open = false">
                    <template x-for="(c, i) in filtered" :key="c.id">
                        <button type="button"
                                class="w-full text-left px-4 py-3 text-sm text-white hover:bg-white/15 border-b border-white/10 last:border-0 transition-colors"
                                :class="{ 'bg-cyan-500/25': i === focusedIndex }"
                                @click="select(c)">
                            <span x-text="c.nom"></span>
                            <span x-show="c.pseudo" class="text-white/50 ml-1" x-text="' (@' + c.pseudo + ')'"></span>
                        </button>
                    </template>
                </div>
                <p class="mt-1 text-xs text-[#64748b]">Tapez le nom pour afficher les créateurs. Pour enlever une personne qui n'existe pas : sélectionne-la puis « Supprimer ce créateur » en haut à droite.</p>
                <?php $__errorArgs = ['createur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php if($createurSelect): ?>
            <div>
                <label for="details_infraction" class="block text-sm font-medium text-[#94a3b8] mb-1">Détails de l'infraction <span class="text-red-400">*</span></label>
                <textarea name="details_infraction" id="details_infraction" rows="3" required class="ultra-input w-full px-4 py-3 rounded-xl text-white text-sm border border-white/10 bg-white/5 placeholder-[#64748b]" placeholder="Décrire l'infraction ou la raison de la modification du score..."><?php echo e(old('details_infraction')); ?></textarea>
                <?php $__errorArgs = ['details_infraction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="score_consequent" class="block text-sm font-medium text-[#94a3b8] mb-1">Nouveau score (0–100) <span class="text-red-400">*</span></label>
                    <input type="number" name="score_consequent" id="score_consequent" value="<?php echo e(old('score_consequent', $scoreActuel)); ?>" min="0" max="100" required class="ultra-input w-full px-4 py-3 rounded-xl text-white text-sm border border-white/10 bg-white/5 tabular-nums">
                    <p class="mt-1 text-xs text-[#64748b]">Score actuel : <?php echo e($scoreActuel); ?>. Baisser pour une infraction, augmenter pour une régularisation.</p>
                    <?php $__errorArgs = ['score_consequent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="sanction_infraction" class="block text-sm font-medium text-[#94a3b8] mb-1">Sanction (optionnel)</label>
                    <input type="text" name="sanction_infraction" id="sanction_infraction" value="<?php echo e(old('sanction_infraction')); ?>" class="ultra-input w-full px-4 py-3 rounded-xl text-white text-sm border border-white/10 bg-white/5" placeholder="Ex. Avertissement, mise en garde...">
                    <?php $__errorArgs = ['sanction_infraction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" class="ultra-btn-cta px-5 py-2.5 text-sm"><span>Enregistrer l'infraction / Mettre à jour le score</span></button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="rounded-2xl border border-white/15 overflow-hidden bg-white/[0.07]">
        <div class="px-5 py-4 border-b border-white/15 bg-white/5">
            <h2 class="text-lg font-bold text-white">Dernières modifications</h2>
            <p class="text-[#94a3b8] text-sm mt-0.5">Historique des infractions et mises à jour de score.</p>
        </div>
        <?php if($historique->isEmpty()): ?>
        <div class="p-8 text-center text-[#94a3b8] text-sm">Aucune modification pour le moment.</div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="text-left border-b-2 border-white/20 bg-white/10">
                        <th class="py-3 px-4 font-semibold text-white/90">Créateur</th>
                        <th class="py-3 px-4 font-semibold text-white/90 whitespace-nowrap">Date</th>
                        <th class="py-3 px-4 font-semibold text-white/90">Détails</th>
                        <th class="py-3 px-4 font-semibold text-white/90 text-center">Score avant</th>
                        <th class="py-3 px-4 font-semibold text-white/90 text-center">Score après</th>
                        <th class="py-3 px-4 font-semibold text-white/90">Sanction</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $historique; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b border-white/10 hover:bg-white/5 transition-colors <?php echo e($loop->even ? 'bg-white/[0.03]' : ''); ?>">
                        <td class="py-3.5 px-4 font-medium text-white"><?php echo e($h->createur?->nom ?? '—'); ?></td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-[#b0bee3]"><?php echo e($h->heure_modification?->translatedFormat('d/m/Y H:i') ?? '—'); ?></td>
                        <td class="py-3.5 px-4 text-white/95 max-w-xs"><?php echo e(Str::limit($h->details_infraction, 60)); ?></td>
                        <td class="py-3.5 px-4 tabular-nums text-center text-[#94a3b8]"><?php echo e($h->score_avant); ?></td>
                        <td class="py-3.5 px-4 tabular-nums text-center font-semibold text-white"><?php echo e($h->score_consequent); ?></td>
                        <td class="py-3.5 px-4 text-white/90"><?php echo e($h->sanction_infraction ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/score-integrite/gestion.blade.php ENDPATH**/ ?>