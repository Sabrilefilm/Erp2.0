<?php if($paginator->hasPages()): ?>
<nav role="navigation" class="flex items-center justify-between gap-2 flex-wrap">
    <div class="flex items-center gap-2">
        <?php if($paginator->onFirstPage()): ?>
        <span class="px-3 py-1.5 rounded-lg ultra-input cursor-not-allowed text-[#6b7a9f]">Précédent</span>
        <?php else: ?>
        <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="px-3 py-1.5 rounded-lg ultra-btn-primary text-white hover:opacity-90 transition-opacity"><span>Précédent</span></a>
        <?php endif; ?>
        <?php if($paginator->hasMorePages()): ?>
        <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="px-3 py-1.5 rounded-lg ultra-btn-primary text-white hover:opacity-90 transition-opacity"><span>Suivant</span></a>
        <?php else: ?>
        <span class="px-3 py-1.5 rounded-lg ultra-input cursor-not-allowed text-[#6b7a9f]">Suivant</span>
        <?php endif; ?>
    </div>
    <p class="text-sm text-[#b0bee3]">
        Page <?php echo e($paginator->currentPage()); ?> sur <?php echo e($paginator->lastPage()); ?>

    </p>
</nav>
<?php endif; ?>
<?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/vendor/pagination/default.blade.php ENDPATH**/ ?>