<?php $__env->startSection('title', 'Récompenses'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-8">
    
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-emerald-500/20 via-teal-500/10 to-neon-green/5 border border-white/10 p-4 md:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 md:gap-6">
            <div class="flex items-center gap-3 md:gap-4 min-w-0">
                <div class="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-neon-green/20 flex items-center justify-center shrink-0 shadow-lg shadow-neon-green/20">
                    <span class="text-2xl md:text-3xl">🏆</span>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-white">Récompenses</h1>
                    <p class="text-[#94a3b8] text-xs md:text-sm mt-0.5"><?php if(auth()->user()->canAttribuerOuRecupererRecompense()): ?> Attribuez une récompense et consultez les informations (liste, factures, codes). <?php else: ?> Consultez les récompenses de votre périmètre. La facture est disponible quelques secondes après avoir choisi le mode de réception. Seul le fondateur peut attribuer une récompense. <?php endif; ?></p>
                </div>
            </div>
            
            <div class="shrink-0">
                <div class="rounded-xl border border-neon-green/30 bg-neon-green/10 px-4 py-3 text-center min-w-[160px]">
                    <p class="text-[10px] font-semibold text-neon-green/90 uppercase tracking-wider">À réceptionner (pour vous)</p>
                    <p class="text-[11px] text-[#94a3b8] mt-1">Solde disponible</p>
                    <?php if($montantAReceptionner > 0): ?>
                    <p class="text-lg font-bold text-neon-green tabular-nums mt-0.5"><?php echo e(number_format($montantAReceptionner, 2, ',', ' ')); ?> €</p>
                    <p class="text-[10px] text-[#94a3b8] mt-1">Choisissez comment recevoir ci-dessous</p>
                    <?php else: ?>
                    <p class="text-lg font-bold text-white/80 tabular-nums mt-0.5">0,00 €</p>
                    <p class="text-[10px] text-amber-300/80 mt-1">Bientôt disponible</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    

    <?php if($recompensesEnAttenteChoix->isNotEmpty()): ?>
    <?php $firstRecompenseId = $recompensesEnAttenteChoix->first()->id; $hasMultiple = $recompensesEnAttenteChoix->count() > 1; ?>
    
    <p class="text-white font-semibold text-sm md:text-base mb-3 md:mb-4">Choisissez une option ci-dessous (virement, TikTok ou carte cadeau), puis cliquez Confirmer.</p>
    <?php if($recompensesEnAttenteChoix->count() > 1): ?>
    <p class="text-[#94a3b8] text-sm mb-2">Total à réceptionner : <strong class="text-neon-green"><?php echo e(number_format($montantAReceptionner, 2, ',', ' ')); ?> €</strong> (<?php echo e($recompensesEnAttenteChoix->count()); ?> récompenses — les montants s’accumulent).</p>
    <?php endif; ?>
    <div class="space-y-2 recompense-choix-accordion" x-data="{ openId: <?php echo e($firstRecompenseId); ?> }">
    <?php $__currentLoopData = $recompensesEnAttenteChoix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rAttente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="rounded-xl md:rounded-2xl border border-white/10 bg-white/[0.03] overflow-hidden recompense-choix-block">
        <?php if($hasMultiple): ?>
        <button type="button" class="w-full px-3 py-3 md:px-4 md:py-3 flex items-center justify-between gap-2 text-left hover:bg-white/5 transition-colors recompense-choix-header" @click="openId = <?php echo e($rAttente->id); ?>" x-show="openId !== <?php echo e($rAttente->id); ?>" x-transition>
            <span class="font-semibold text-neon-green tabular-nums"><?php echo e(number_format($rAttente->montant, 2, ',', ' ')); ?> €</span>
            <?php if($rAttente->raison): ?><span class="text-white/70 text-sm truncate"><?php echo e($rAttente->raison); ?></span><?php endif; ?>
            <span class="text-[#64748b] text-xs flex-shrink-0">Choisir le mode de réception →</span>
        </button>
        <?php endif; ?>
        <div class="recompense-choix-body" x-show="openId === <?php echo e($rAttente->id); ?>" x-transition>
        <form action="<?php echo e(route('recompenses.choisir-type', $rAttente)); ?>" method="POST" class="p-3 md:p-5 lg:p-6 space-y-4 md:space-y-6 recompense-choix-form <?php echo e($hasMultiple ? 'border-t border-white/10' : ''); ?>" data-recompense-id="<?php echo e($rAttente->id); ?>">
            <?php echo csrf_field(); ?>
            
            <?php $oldType = old('type', ''); ?>
            <div class="hidden" aria-hidden="true">
                <input type="radio" name="type" value="virement" id="type_virement_<?php echo e($rAttente->id); ?>" class="recompense-type-radio" <?php echo e($oldType === 'virement' ? 'checked' : ''); ?>>
                <input type="radio" name="type" value="tiktok" id="type_tiktok_<?php echo e($rAttente->id); ?>" class="recompense-type-radio" <?php echo e($oldType === 'tiktok' ? 'checked' : ''); ?>>
                <input type="radio" name="type" value="carte_cadeau" id="type_carte_cadeau_<?php echo e($rAttente->id); ?>" class="recompense-type-radio" <?php echo e($oldType === 'carte_cadeau' ? 'checked' : ''); ?>>
            </div>
            <div>
                <p class="text-xs md:text-sm font-semibold text-white mb-2 md:mb-3">Choisir :</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 md:gap-4">
                    <button type="button" data-recompense-type="virement" class="recompense-choix-card flex flex-col items-center justify-center gap-1 min-h-[100px] md:min-h-[140px] p-3 md:p-5 rounded-xl md:rounded-2xl border-2 border-white/20 bg-blue-500/10 hover:border-blue-400 hover:bg-blue-500/20 active:scale-[0.98] transition-all cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a]">
                        <span class="text-2xl md:text-3xl" aria-hidden="true">🏦</span>
                        <span class="font-bold text-white text-sm md:text-base">Virement bancaire</span>
                        <span class="text-[10px] md:text-xs text-[#94a3b8] hidden sm:inline">Sur mon compte</span>
                        <span class="text-[9px] md:text-[10px] uppercase tracking-wider text-blue-300/80">Choisir</span>
                    </button>
                    <button type="button" data-recompense-type="tiktok" class="recompense-choix-card flex flex-col items-center justify-center gap-1 min-h-[100px] md:min-h-[140px] p-3 md:p-5 rounded-xl md:rounded-2xl border-2 border-white/20 bg-black/30 hover:border-pink-400 hover:bg-pink-500/20 active:scale-[0.98] transition-all cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a]">
                        <span class="text-2xl md:text-3xl" aria-hidden="true">🎬</span>
                        <span class="font-bold text-white text-sm md:text-base">Cadeau TikTok</span>
                        <span class="text-[10px] md:text-xs text-[#94a3b8] hidden sm:inline">50 % en cadeaux live</span>
                        <span class="text-[9px] md:text-[10px] uppercase tracking-wider text-pink-300/80">Choisir</span>
                    </button>
                    <button type="button" data-recompense-type="carte_cadeau" class="recompense-choix-card flex flex-col items-center justify-center gap-1 min-h-[100px] md:min-h-[140px] p-3 md:p-5 rounded-xl md:rounded-2xl border-2 border-white/20 bg-amber-500/10 hover:border-amber-400 hover:bg-amber-500/20 active:scale-[0.98] transition-all cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a]">
                        <span class="text-2xl md:text-3xl" aria-hidden="true">🎟️</span>
                        <span class="font-bold text-white text-sm md:text-base">Carte cadeau</span>
                        <span class="text-[10px] md:text-xs text-[#94a3b8] hidden sm:inline">Code sur facture · Valable 1 fois, expire sous 1 an</span>
                        <span class="text-[9px] md:text-[10px] uppercase tracking-wider text-amber-300/90">Choisir</span>
                    </button>
                </div>
            </div>

            
            <div class="recompense-panel recompense-panel-virement hidden rounded-xl border border-blue-500/30 bg-blue-500/10 p-4 space-y-4">
                <p class="text-sm font-semibold text-white">Coordonnées bancaires.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-white/80 mb-1">Nom *</label>
                        <input type="text" name="rib_nom" value="<?php echo e(old('rib_nom')); ?>" class="w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" maxlength="120" placeholder="Dupont">
                    </div>
                    <div>
                        <label class="block text-xs text-white/80 mb-1">Prénom *</label>
                        <input type="text" name="rib_prenom" value="<?php echo e(old('rib_prenom')); ?>" class="w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" maxlength="120" placeholder="Jean">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-white/80 mb-1">IBAN * (la banque se remplit automatiquement)</label>
                    <input type="text" name="rib_iban" id="rib_iban" value="<?php echo e(old('rib_iban')); ?>" class="recompense-rib-iban w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm font-mono" maxlength="50" placeholder="FR76 1234 5678 9012 3456 7890 123" autocomplete="off">
                    <p class="text-[10px] text-blue-200/70 mt-1 recompense-iban-status" aria-live="polite"></p>
                </div>
                <div>
                    <label class="block text-xs text-white/80 mb-1">Nom de la banque * (rempli automatiquement depuis l’IBAN, modifiable)</label>
                    <input type="text" name="rib_banque" id="rib_banque" value="<?php echo e(old('rib_banque')); ?>" class="recompense-rib-banque w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" maxlength="120" placeholder="Saisissez l’IBAN ci-dessus pour trouver la banque" list="liste-banques" autocomplete="organization">
                    <datalist id="liste-banques">
                        <option value="BNP Paribas">
                        <option value="Société Générale">
                        <option value="Crédit Agricole">
                        <option value="CIC - Crédit Industriel et Commercial">
                        <option value="Crédit Mutuel">
                        <option value="Caisse d'Épargne">
                        <option value="Banque Populaire">
                        <option value="La Banque Postale">
                        <option value="HSBC France">
                        <option value="Boursobank (ex-Boursorama)">
                        <option value="Hello bank!">
                        <option value="Fortuneo">
                        <option value="ING">
                        <option value="AXA Banque">
                        <option value="Crédit du Nord">
                        <option value="LCL">
                        <option value="Monabanq">
                        <option value="N26">
                        <option value="Revolut">
                        <option value="Orange Bank">
                        <option value="Nickel">
                        <option value="Compte Nickel">
                        <option value="Qonto">
                        <option value="Shine">
                        <option value="Monese">
                        <option value="BforBank">
                        <option value="CIC Banque">
                        <option value="Banque Kolb">
                        <option value="Crédit Coopératif">
                        <option value="Banque Palatine">
                        <option value="BRED">
                        <option value="BPI France">
                        <option value="Caisse d'Épargne Île-de-France">
                        <option value="Crédit Mutuel Arkéa">
                        <option value="Crédit Mutuel Alliance Fédérale">
                        <option value="Banque Populaire du Nord">
                        <option value="Banque Populaire Rives de Paris">
                        <option value="CIC Est">
                        <option value="CIC Ouest">
                        <option value="CIC Lyonnaise de Banque">
                        <option value="CIC Nord Ouest">
                        <option value="CIC Sud-Ouest">
                        <option value="CIC Bourgogne Franche-Comté">
                    </datalist>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="rib_confirme" value="1" class="rounded accent-neon-green" <?php echo e(old('rib_confirme') ? 'checked' : ''); ?>>
                    <span class="text-sm text-white/90">J’atteste que ces informations sont exactes *</span>
                </label>
                <?php $__errorArgs = ['rib_confirme'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-amber-400 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-xs font-normal text-white/80 mt-1">Facture disponible *</p>
            </div>

            <div class="recompense-panel recompense-panel-tiktok hidden rounded-xl border border-pink-500/30 bg-black/20 p-4 flex flex-wrap gap-4">
                <p class="text-sm font-semibold text-white w-full">Date et heure du cadeau en live.</p>
                <div>
                    <label class="block text-xs text-white/80 mb-1">Date * (entre aujourd’hui et 2 mois)</label>
                    <input type="date" name="date_cadeau_tiktok" value="<?php echo e(old('date_cadeau_tiktok')); ?>" class="px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" min="<?php echo e(now()->format('Y-m-d')); ?>" max="<?php echo e(now()->addMonths(2)->format('Y-m-d')); ?>" required>
                    <?php $__errorArgs = ['date_cadeau_tiktok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-amber-400 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs text-white/80 mb-1">Heure *</label>
                    <input type="time" name="heure_cadeau_tiktok" value="<?php echo e(old('heure_cadeau_tiktok')); ?>" class="px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm">
                    <?php $__errorArgs = ['heure_cadeau_tiktok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-amber-400 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <p class="text-xs font-normal text-white/80 w-full mt-1">Facture disponible *</p>
            </div>

            <?php if($errors->has('type_carte_cadeau') || $errors->has('type')): ?>
            <div class="rounded-xl bg-red-500/20 border border-red-400/50 text-red-200 text-sm px-4 py-3 flex items-start gap-2" role="alert">
                <span class="text-lg shrink-0" aria-hidden="true">⚠️</span>
                <p class="font-medium"><?php echo e($errors->first('type_carte_cadeau') ?: $errors->first('type')); ?></p>
            </div>
            <?php endif; ?>
            <div class="pt-4 recompense-submit-area">
                <button type="submit" class="recompense-submit-btn inline-flex items-center justify-center gap-2 px-10 py-4 rounded-2xl bg-neon-green text-white font-bold text-base border-0 shadow-lg shadow-neon-green/40 opacity-70 hover:opacity-100 hover:brightness-110 hover:shadow-xl active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a] cursor-pointer">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Confirmer
                </button>
                <p class="text-xs text-[#64748b] mt-2 recompense-hint hidden">Choisissez une option ci-dessous (virement, TikTok ou carte cadeau), puis cliquez Confirmer.</p>
            </div>
        </form>

            
            <?php $montantsDisponibles = array_filter(\App\Models\Recompense::MONTANTS_CARTE_CADEAU, fn($m) => $m <= (float) $rAttente->montant); ?>
            <div class="recompense-panel recompense-panel-carte_cadeau hidden rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 space-y-3 mt-4">
                <p class="text-sm font-semibold text-amber-100">Choisissez votre carte cadeau.</p>
                <p class="text-xs text-amber-200/80">Solde disponible : <strong><?php echo e(number_format($rAttente->montant, 2, ',', ' ')); ?> €</strong>. Le code est valable une seule fois dès utilisation, et expire 1 an à compter de la date d'achat.</p>
                <form action="<?php echo e(route('recompenses.choisir-type', $rAttente)); ?>" method="POST" class="space-y-3 formulaire-carte-cadeau" data-montant="<?php echo e($rAttente->montant); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="type" value="carte_cadeau">
                    <div>
                        <label class="block text-xs text-amber-100/90 mb-1.5">Type de carte cadeau *</label>
                        <select name="type_carte_cadeau" required class="w-full max-w-md px-4 py-3 rounded-xl bg-white/10 border text-amber-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 <?php echo e($errors->has('type_carte_cadeau') ? 'border-red-400 bg-red-500/10' : 'border-amber-400/40'); ?>" aria-label="Type de carte cadeau">
                            <option value="">— Choisir une carte cadeau —</option>
                            <?php $__currentLoopData = \App\Models\Recompense::TYPES_CARTE_CADEAU; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('type_carte_cadeau', '') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['type_carte_cadeau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 px-3 py-2 rounded-lg bg-red-500/20 border border-red-400/50 text-red-200 text-sm font-medium flex items-center gap-2" role="alert">
                            <span aria-hidden="true">⚠️</span>
                            <?php echo e($message); ?>

                        </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                        <div>
                            <label class="block text-xs text-amber-100/90 mb-1.5">Montant de la carte *</label>
                            <select name="montant_carte_cadeau" required class="cc-montant w-full px-4 py-3 rounded-xl bg-white/10 border border-amber-400/40 text-amber-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" aria-label="Montant">
                                <option value="">— Montant —</option>
                                <?php $__currentLoopData = $montantsDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($m); ?>" <?php echo e(old('montant_carte_cadeau', '') == $m ? 'selected' : ''); ?>><?php echo e(number_format($m, 0, ',', ' ')); ?> €</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['montant_carte_cadeau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-400"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-xs text-amber-100/90 mb-1.5">Quantité *</label>
                            <select name="quantite_carte_cadeau" required class="cc-quantite w-full px-4 py-3 rounded-xl bg-white/10 border border-amber-400/40 text-amber-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" aria-label="Quantité">
                                <option value="">— Quantité —</option>
                                <?php $oldM = old('montant_carte_cadeau'); $oldQ = old('quantite_carte_cadeau', 1); $maxQte = ($oldM !== '' && $oldM !== null && (float)$oldM > 0) ? (int) floor((float)$rAttente->montant / (float)$oldM) : 0; ?>
                                <?php for($q = 1; $q <= $maxQte; $q++): ?>
                                <option value="<?php echo e($q); ?>" <?php echo e((int)$oldQ === $q ? 'selected' : ''); ?>><?php echo e($q); ?></option>
                                <?php endfor; ?>
                            </select>
                            <?php $__errorArgs = ['quantite_carte_cadeau'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-400"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-neon-green text-white font-bold text-base border-0 shadow-lg shadow-neon-green/40 opacity-70 hover:opacity-100 hover:brightness-110 hover:shadow-xl active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a] cursor-pointer">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <script>
    window.ibanBanqueUrl = <?php echo e(Js::from(route('recompenses.iban-banque'))); ?>;
    (function() {
        document.querySelectorAll('.recompense-choix-block').forEach(function(block) {
            var form = block.querySelector('.recompense-choix-form');
            var cards = block.querySelectorAll('.recompense-choix-card');
            var panels = block.querySelectorAll('.recompense-panel');
            var submitBtn = block.querySelector('.recompense-submit-btn');
            var hint = block.querySelector('.recompense-hint');

            function getCheckedType() {
                var r = form.querySelector('input[name="type"]:checked');
                return r ? r.value : '';
            }

            var submitArea = block.querySelector('.recompense-submit-area');
            var panelCarteCadeau = block.querySelector('.recompense-panel-carte_cadeau');

            function selectType(type) {
                form.setAttribute('data-selected-type', type || '');
                var radio = form.querySelector('input[name="type"][value="' + (type || '') + '"]');
                if (radio) radio.checked = true;
                cards.forEach(function(card) {
                    var isSelected = card.getAttribute('data-recompense-type') === type;
                    card.classList.toggle('recompense-card-selected', isSelected);
                    card.classList.toggle('!border-neon-green', isSelected);
                    card.classList.toggle('!bg-neon-green/20', isSelected);
                    card.classList.toggle('!ring-2', isSelected);
                    card.classList.toggle('!ring-neon-green/50', isSelected);
                });
                panels.forEach(function(panel) {
                    var panelType = panel.classList.contains('recompense-panel-virement') ? 'virement' : (panel.classList.contains('recompense-panel-tiktok') ? 'tiktok' : 'carte_cadeau');
                    panel.classList.toggle('hidden', panelType !== type);
                    if (panelType === type && panelType === 'tiktok') panel.classList.add('flex');
                });
                if (panelCarteCadeau) panelCarteCadeau.classList.toggle('hidden', type !== 'carte_cadeau');
                if (submitArea) submitArea.classList.toggle('hidden', type === 'carte_cadeau');
                hint.classList.toggle('hidden', !!type);
            }

            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    selectType(card.getAttribute('data-recompense-type'));
                });
            });

            var initialType = getCheckedType();
            if (initialType) selectType(initialType);

            form.addEventListener('submit', function(e) {
                var t = getCheckedType() || form.getAttribute('data-selected-type') || '';
                var selectedCard = block.querySelector('.recompense-choix-card.recompense-card-selected');
                if (!t && selectedCard) t = selectedCard.getAttribute('data-recompense-type');
                if (t) {
                    var radio = form.querySelector('input[name="type"][value="' + t + '"]');
                    if (radio) radio.checked = true;
                }
                if (t === 'carte_cadeau') {
                    e.preventDefault();
                    return;
                }
                if (!t) {
                    e.preventDefault();
                    hint.classList.remove('hidden');
                    hint.textContent = 'Choisissez d’abord une option (virement, TikTok ou carte cadeau) en cliquant sur une carte.';
                    return;
                }
            });
        });

        // Remplissage automatique du nom de la banque à partir de l’IBAN
        document.querySelectorAll('.formulaire-carte-cadeau').forEach(function(form) {
            var montantTotal = parseFloat(form.getAttribute('data-montant')) || 0;
            var selectMontant = form.querySelector('.cc-montant');
            var selectQuantite = form.querySelector('.cc-quantite');
            function mettreAJourQuantite() {
                var val = parseFloat(selectMontant.value) || 0;
                var selectedQte = selectQuantite.value;
                selectQuantite.innerHTML = '<option value="">— Quantité —</option>';
                if (val > 0 && montantTotal >= val) {
                    var maxQte = Math.floor(montantTotal / val);
                    for (var q = 1; q <= maxQte; q++) {
                        var opt = document.createElement('option');
                        opt.value = q;
                        opt.textContent = q;
                        if (selectedQte && parseInt(selectedQte, 10) === q) opt.selected = true;
                        selectQuantite.appendChild(opt);
                    }
                }
            }
            if (selectMontant && selectQuantite) {
                selectMontant.addEventListener('change', mettreAJourQuantite);
                mettreAJourQuantite();
            }
        });
        var ibanTimeout;
        document.querySelectorAll('.recompense-rib-iban').forEach(function(ibanInput) {
            var form = ibanInput.closest('form');
            var banqueInput = form.querySelector('.recompense-rib-banque');
            var statusEl = form.querySelector('.recompense-iban-status');

            function fetchBanque() {
                var iban = ibanInput.value.replace(/\s/g, '').toUpperCase();
                if (iban.length < 15) {
                    statusEl.textContent = '';
                    return;
                }
                statusEl.textContent = 'Recherche de la banque…';
                statusEl.classList.remove('text-neon-green', 'text-amber-300');
                fetch(window.ibanBanqueUrl + '?iban=' + encodeURIComponent(iban), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.bank) {
                            banqueInput.value = data.bank;
                            statusEl.textContent = 'Banque trouvée : ' + data.bank;
                            statusEl.classList.add('text-neon-green');
                        } else {
                            statusEl.textContent = data.error || 'Banque non trouvée pour cet IBAN. Saisissez le nom manuellement.';
                            statusEl.classList.add('text-amber-300');
                        }
                    })
                    .catch(function() {
                        statusEl.textContent = 'Recherche indisponible. Saisissez le nom de la banque.';
                        statusEl.classList.add('text-amber-300');
                    });
            }

            ibanInput.addEventListener('blur', fetchBanque);
            ibanInput.addEventListener('input', function() {
                clearTimeout(ibanTimeout);
                var iban = ibanInput.value.replace(/\s/g, '');
                if (iban.length >= 15) {
                    ibanTimeout = setTimeout(fetchBanque, 500);
                } else {
                    statusEl.textContent = '';
                }
            });
        });
    })();
    </script>
    <?php endif; ?>

    <?php if(auth()->user()->canAttribuerOuRecupererRecompense()): ?>
    
    <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-violet-500/5 to-neon-purple/5 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <span class="text-xl">🎁</span> Attribuer une récompense
        </h2>
        <p class="text-sm text-[#94a3b8] mb-4">Le créateur pourra ensuite choisir le mode de réception : virement bancaire, cadeau TikTok ou carte cadeau.</p>
        <form action="<?php echo e(route('recompenses.store')); ?>" method="POST" class="space-y-4" id="form-recompense">
            <?php echo csrf_field(); ?>
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-0">
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Créateur *</label>
                    <select name="createur_id" required class="w-full min-w-[200px] max-w-[280px] px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-neon-green/50 focus:ring-1 focus:ring-neon-green/30 transition-colors">
                        <option value="">Choisir un créateur</option>
                        <?php $__empty_1 = true; $__currentLoopData = $createurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($c->id); ?>"><?php echo e($c->nom); ?><?php echo e($c->pseudo_tiktok ? ' (@' . $c->pseudo_tiktok . ')' : ''); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </select>
                    <?php if($createurs->isEmpty()): ?>
                    <p class="mt-1 text-xs text-neon-orange">Aucun créateur dans votre périmètre.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Montant (€) * <span class="text-[#64748b] font-normal">(minimum 0 €)</span></label>
                    <input type="number" name="montant" id="recompense-montant" step="0.01" min="0" required placeholder="0,00" value="<?php echo e(old('montant')); ?>" class="w-24 px-4 py-2.5 rounded-xl bg-white/5 border text-white text-sm focus:outline-none focus:border-neon-green/50 focus:ring-1 focus:ring-neon-green/30 transition-colors <?php echo e($errors->has('montant') ? 'border-red-400' : 'border-white/10'); ?>">
                    <?php $__errorArgs = ['montant'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-400 font-medium" role="alert"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="min-w-0 flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Raison (optionnel)</label>
                    <input type="text" name="raison" placeholder="Ex. Top du mois" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:outline-none focus:border-neon-green/50 focus:ring-1 focus:ring-neon-green/30 transition-colors">
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button type="submit" class="ultra-btn-cta inline-flex items-center gap-2 cursor-pointer border-0"><span><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg> Attribuer</span></button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <span>📋</span> Historique
            </h2>
            <?php if($recompenses->total() > 0): ?>
            <span class="text-xs text-[#64748b]"><?php echo e($recompenses->total()); ?> récompense<?php echo e($recompenses->total() > 1 ? 's' : ''); ?></span>
            <?php endif; ?>
        </div>

        <?php if($recompenses->isEmpty()): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4 text-4xl">🎁</div>
            <p class="text-[#94a3b8]">Aucune récompense pour l’instant.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 text-left text-[#64748b]">
                        <th class="px-5 py-3 font-semibold">Date</th>
                        <?php if(auth()->user()->canAttribuerOuRecupererRecompense()): ?>
                        <th class="px-5 py-3 font-semibold">Créateur</th>
                        <?php endif; ?>
                        <th class="px-5 py-3 font-semibold">Type</th>
                        <th class="px-5 py-3 font-semibold text-right">Montant</th>
                        <th class="px-5 py-3 font-semibold">Raison</th>
                        <th class="px-5 py-3 font-semibold">Facture</th>
                        <?php if(auth()->user()->canAttribuerOuRecupererRecompense()): ?>
                        <th class="px-5 py-3 font-semibold text-right w-28">Actions</th>
                        <?php else: ?>
                        <th class="px-5 py-3 font-semibold text-right">Détail</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recompenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $typeLabel = \App\Models\Recompense::TYPES[$r->type] ?? $r->type;
                        $isTiktok = $r->type === 'tiktok' || $r->type === 'TikTok';
                        $enAttenteChoix = $r->isEnAttenteChoix();
                        $supprime = $r->trashed();
                    ?>
                    <tr class="border-b border-white/5 transition-colors <?php echo e($supprime ? 'opacity-60 bg-red-500/5' : 'hover:bg-white/5'); ?>">
                        <td class="px-5 py-3.5 text-[#94a3b8]"><?php echo e($r->created_at->format('d/m/Y')); ?> <span class="text-[#64748b]"><?php echo e($r->created_at->format('H:i')); ?></span> <?php if($supprime): ?><span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-500/30 text-red-300 border border-red-500/40">Supprimé</span><?php endif; ?></td>
                        <?php if(auth()->user()->canAttribuerOuRecupererRecompense()): ?>
                        <td class="px-5 py-3.5 text-white font-medium"><?php echo e($r->createur->nom); ?></td>
                        <?php endif; ?>
                        <td class="px-5 py-3.5">
                            <?php if($enAttenteChoix): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-amber-500/20 text-amber-300 border border-amber-500/30">En attente de choix</span>
                            <?php elseif($isTiktok): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-black/30 text-white border border-white/10">TikTok</span>
                            <?php elseif($r->type === 'carte_cadeau' || $r->type === 'Carte cadeau'): ?>
                            <span class="inline-flex flex-col gap-0.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-amber-500/20 text-amber-300 border border-amber-500/30">Carte cadeau</span>
                                <?php if($r->type_carte_cadeau): ?>
                                <span class="text-[10px] text-[#94a3b8]"><?php echo e(\App\Models\Recompense::TYPES_CARTE_CADEAU[$r->type_carte_cadeau] ?? $r->type_carte_cadeau); ?></span>
                                <?php endif; ?>
                            </span>
                            <?php elseif($r->type === 'virement' || $r->type === 'PayPal'): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">Virement</span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-300 border border-white/10"><?php echo e($typeLabel); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <?php if($enAttenteChoix): ?>
                            <span class="font-bold text-neon-green tabular-nums"><?php echo e(number_format($r->montant, 2, ',', ' ')); ?> €</span>
                            <?php elseif($isTiktok): ?>
                            <span class="font-bold text-neon-green tabular-nums">+ <?php echo e(number_format($r->montant_tiktok, 2, ',', ' ')); ?> €</span>
                            <span class="block text-[10px] text-[#64748b]">(50 % de <?php echo e(number_format($r->montant, 2, ',', ' ')); ?> €)</span>
                            <?php else: ?>
                            <span class="font-bold text-neon-green tabular-nums">+ <?php echo e(number_format($r->montant, 2, ',', ' ')); ?> €</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5 text-[#94a3b8]"><?php echo e($r->raison ?? '—'); ?></td>
                        <td class="px-5 py-3.5 text-[#64748b] text-xs">
                            <?php if($supprime): ?>
                            <span class="text-red-400/80">—</span>
                            <?php elseif(!$enAttenteChoix): ?>
                            <?php if($r->factureEstDisponible()): ?>
                            <a href="<?php echo e(route('recompenses.facture', $r)); ?>" target="_blank" rel="noopener" class="text-cyan-400 hover:text-cyan-300">Téléchargement disponible pour la facture. Cliquez ici</a>
                            <?php else: ?>
                            <span class="text-amber-400/90" x-data="{ sec: <?php echo e($r->secondesRestantesFacture()); ?> }" x-init="setInterval(() => { sec = Math.max(0, sec - 1) }, 1000)">
                                <span x-show="sec > 0" x-transition>Disponible dans <span x-text="sec"></span> s</span>
                                <a x-show="sec <= 0" x-transition href="<?php echo e(route('recompenses.facture', $r)); ?>" target="_blank" rel="noopener" class="text-cyan-400 hover:text-cyan-300">Téléchargement disponible pour la facture. Cliquez ici</a>
                            </span>
                            <?php endif; ?>
                            <?php else: ?>
                            <span>—</span>
                            <?php endif; ?>
                        </td>
                        <?php if(!auth()->user()->canAttribuerOuRecupererRecompense()): ?>
                        <td class="px-5 py-3.5 text-right">
                            <?php if(!$supprime): ?>
                            <a href="<?php echo e(route('recompenses.show', $r)); ?>" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-white/10 text-white/90 hover:bg-white/20 text-sm font-medium transition-colors">Voir</a>
                            <?php else: ?>
                            <span class="text-[#64748b]">—</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <?php if(auth()->user()->canAttribuerOuRecupererRecompense()): ?>
                        <td class="px-5 py-3.5 text-right">
                            <?php if($supprime): ?>
                            <span class="text-red-400/90 text-xs font-medium">Supprimé</span>
                            <?php else: ?>
                            <a href="<?php echo e(route('recompenses.show', $r)); ?>" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-neon-green/20 text-neon-green hover:bg-neon-green/30 text-sm font-medium transition-colors mr-2">Voir</a>
                            <form action="<?php echo e(route('recompenses.destroy', $r)); ?>" method="POST" class="inline" onsubmit="return confirm('Supprimer cette récompense ?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 border border-red-500/30 hover:border-red-500/50 text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:ring-offset-2 focus:ring-offset-[#0f172a] cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Supprimer
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if($recompenses->hasPages()): ?>
        <div class="px-5 py-4 border-t border-white/10"><?php echo e($recompenses->links()); ?></div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <p class="text-[10px] md:text-xs text-neon-green/90 text-center pt-2 pb-1" role="note">La somme est créditée entre le 7 et le 12 du mois.</p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/recompenses/index.blade.php ENDPATH**/ ?>