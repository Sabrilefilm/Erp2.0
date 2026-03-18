<?php $__env->startSection('title', 'Créer une annonce'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.form-section {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.form-section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: white;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.type-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.type-option {
    padding: 1.25rem;
    border: 2px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    background: rgba(255,255,255,0.02);
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}
.type-option:hover {
    border-color: rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.05);
}
.type-option.selected {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
}
.type-option-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.type-option-title {
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
}
.type-option-desc {
    font-size: 0.875rem;
    color: rgba(255,255,255,0.6);
}
.field-dependent {
    display: none;
}
.field-dependent.show {
    display: block;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-8 max-w-3xl mx-auto">
    
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-500/25 via-violet-500/15 to-purple-500/10 border border-white/10 p-6 md:p-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-3xl">📝</div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Créer une annonce</h1>
                <p class="text-[#94a3b8] text-sm mt-1">Partagez des annonces, événements ou campagnes TikTok</p>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('annonces.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        
        <div class="form-section">
            <h3 class="form-section-title">🎯 Type d'annonce</h3>
            <div class="type-selector">
                <div class="type-option" data-type="annonce">
                    <div class="type-option-icon">📢</div>
                    <div class="type-option-title">Annonce générale</div>
                    <div class="type-option-desc">Informations pour toute l'équipe</div>
                </div>
                <div class="type-option" data-type="evenement">
                    <div class="type-option-icon">🎉</div>
                    <div class="type-option-title">Événement TikTok</div>
                    <div class="type-option-desc">Meetups, lancements, etc.</div>
                </div>
                <div class="type-option" data-type="campagne">
                    <div class="type-option-icon">🎯</div>
                    <div class="type-option-title">Campagne TikTok</div>
                    <div class="type-option-desc">Hashtags, objectifs, liens</div>
                </div>
            </div>
            <input type="hidden" name="type" id="type_input" value="annonce" required>
        </div>

        
        <div class="form-section">
            <h3 class="form-section-title">📋 Informations générales</h3>
            
            <div class="space-y-4">
                <div>
                    <label for="titre" class="block text-sm font-medium text-white mb-2">Titre *</label>
                    <input type="text" name="titre" id="titre" required maxlength="255"
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"
                           placeholder="Titre accrocheur...">
                </div>

                <div>
                    <label for="contenu" class="block text-sm font-medium text-white mb-2">Contenu *</label>
                    <textarea name="contenu" id="contenu" required rows="6"
                              class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"
                              placeholder="Décrivez votre annonce, événement ou campagne... Les emojis sont bienvenus ! ✨"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="ordre" class="block text-sm font-medium text-white mb-2">Ordre d'affichage</label>
                        <input type="number" name="ordre" id="ordre" min="0"
                               class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"
                               placeholder="0 (premier)">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="actif" id="actif" value="1" checked class="w-4 h-4 text-indigo-600 bg-white/10 border-white/20 rounded focus:ring-indigo-500">
                        <label for="actif" class="ml-2 text-sm text-white">Publier immédiatement</label>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="form-section field-dependent" id="evenement_fields">
            <h3 class="form-section-title">📅 Détails de l'événement</h3>
            
            <div class="space-y-4">
                <div>
                    <label for="date_evenement" class="block text-sm font-medium text-white mb-2">Date et heure de l'événement *</label>
                    <input type="datetime-local" name="date_evenement" id="date_evenement"
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                </div>

                <div>
                    <label for="lieu_evenement" class="block text-sm font-medium text-white mb-2">Lieu de l'événement *</label>
                    <input type="text" name="lieu_evenement" id="lieu_evenement" maxlength="255"
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"
                           placeholder="Adresse, lieu, ou en ligne">
                </div>
            </div>
        </div>

        
        <div class="form-section field-dependent" id="campagne_fields">
            <h3 class="form-section-title">🎯 Détails de la campagne TikTok</h3>
            
            <div class="space-y-4">
                <div>
                    <label for="hashtag_principal" class="block text-sm font-medium text-white mb-2">Hashtag principal *</label>
                    <input type="text" name="hashtag_principal" id="hashtag_principal" maxlength="255"
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"
                           placeholder="sanslediese">
                </div>

                <div>
                    <label for="objectif_campagne" class="block text-sm font-medium text-white mb-2">Objectif de la campagne *</label>
                    <textarea name="objectif_campagne" id="objectif_campagne" rows="3"
                              class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"
                              placeholder="Ex: Atteindre 100k vues, promouvoir un produit, etc."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-white mb-2">Date de début *</label>
                        <input type="datetime-local" name="date_debut" id="date_debut"
                               class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                    </div>

                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-white mb-2">Date de fin *</label>
                        <input type="datetime-local" name="date_fin" id="date_fin"
                               class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20">
                    </div>
                </div>

                <div>
                    <label for="lien_tiktok" class="block text-sm font-medium text-white mb-2">Lien TikTok (optionnel)</label>
                    <input type="url" name="lien_tiktok" id="lien_tiktok"
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/20"
                           placeholder="https://tiktok.com/@votrecompte/video/...">
                </div>
            </div>
        </div>

        
        <div class="flex justify-end gap-4">
            <a href="<?php echo e(route('annonces.index')); ?>" class="px-6 py-3 rounded-xl bg-white/10 text-white font-medium hover:bg-white/15 transition-colors">
                Annuler
            </a>
            <button type="submit" class="ultra-btn-cta">
                <span>Créer l'annonce</span>
            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeOptions = document.querySelectorAll('.type-option');
    const typeInput = document.getElementById('type_input');
    const evenementFields = document.getElementById('evenement_fields');
    const campagneFields = document.getElementById('campagne_fields');

    typeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            typeOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Update hidden input value
            const type = this.dataset.type;
            typeInput.value = type;
            
            // Show/hide dependent fields
            evenementFields.classList.toggle('show', type === 'evenement');
            campagneFields.classList.toggle('show', type === 'campagne');
            
            // Make required fields actually required
            const evenementInputs = evenementFields.querySelectorAll('input[required], textarea[required]');
            const campagneInputs = campagneFields.querySelectorAll('input[required], textarea[required]');
            
            evenementInputs.forEach(input => {
                input.required = type === 'evenement';
            });
            
            campagneInputs.forEach(input => {
                input.required = type === 'campagne';
            });
        });
    });

    // Select first option by default
    typeOptions[0].click();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/annonces/create.blade.php ENDPATH**/ ?>