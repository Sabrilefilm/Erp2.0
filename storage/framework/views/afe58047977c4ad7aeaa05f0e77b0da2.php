<?php $__env->startSection('title', 'Utilisateurs'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-8">
    
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e3a8a] to-[#1e40af] border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-tight">Utilisateurs</h1>
                <p class="text-blue-200/90 text-sm mt-1">Comptes et rôles</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\User::class)): ?>
            <div class="btn-webleb-wrap">
                <a href="<?php echo e(route('users.create')); ?>" class="btn-webleb">
                    <span>+ Créer un utilisateur</span>
                    <span>
                        <svg width="66" height="43" viewBox="0 0 66 43" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path class="one" d="M40.15 3.89L44 0.14c.19-.19.5-.19.68 0L65.69 20.78c.39.39.4 1.02.01 1.41l-21 20.67c-.2.2-.5.2-.68 0l-3.82-3.75c-.2-.2-.2-.52 0-.7L57 21.86c.2-.2.2-.52 0-.7L40.15 4.61c-.2-.2-.2-.52 0-.72z" fill="#fff"/>
                            <path class="two" d="M20.15 3.89L24 0.14c.19-.19.5-.19.68 0L45.69 20.78c.39.39.4 1.02.01 1.41l-21 20.67c-.2.2-.5.2-.68 0l-3.82-3.75c-.2-.2-.2-.52 0-.7L36.99 21.86c.2-.2.2-.52 0-.7L20.15 4.61c-.2-.2-.2-.52 0-.72z" fill="#fff"/>
                            <path class="three" d="M.15 3.89L4 0.14c.19-.19.5-.19.68 0L25.69 20.78c.39.39.4 1.02.01 1.41l-21 20.67c-.2.2-.5.2-.68 0L.15 39.11c-.2-.2-.2-.52 0-.7L17 21.86c.2-.2.2-.52 0-.7L.15 4.61c-.2-.2-.2-.52 0-.72z" fill="#fff"/>
                        </svg>
                    </span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <form action="<?php echo e(route('users.index')); ?>" method="GET" class="flex flex-wrap items-center gap-3 mb-4">
        <input type="hidden" name="role" value="<?php echo e(request('role')); ?>">
        <label for="users-search" class="sr-only">Rechercher un utilisateur</label>
        <input type="search" id="users-search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Nom, email ou identifiant…" class="flex-1 min-w-[200px] max-w-md px-4 py-2.5 rounded-xl text-white bg-white/5 border border-white/10 placeholder-[#64748b] focus:border-sky-500/50 focus:ring-1 focus:ring-sky-500/30 text-sm" autocomplete="off">
        <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-medium bg-sky-500/20 text-sky-400 border border-sky-500/30 hover:bg-sky-500/30 transition-colors">Rechercher</button>
        <?php if(request('q')): ?>
        <a href="<?php echo e(route('users.index', request()->only('role'))); ?>" class="px-4 py-2.5 rounded-xl text-sm font-medium text-[#94a3b8] border border-white/10 hover:bg-white/10">Effacer</a>
        <?php endif; ?>
    </form>

    <div class="flex flex-wrap gap-2 items-center">
        <span class="text-sm text-[#94a3b8]">Rôle :</span>
        <a href="<?php echo e(route('users.index', request()->only('q'))); ?>" class="px-4 py-2 rounded-xl text-sm font-medium <?php echo e(!request('role') ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'bg-white/5 text-[#94a3b8] border border-white/10 hover:bg-white/10'); ?>">Tous</a>
        <?php $__currentLoopData = \App\Models\User::ROLE_LABELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleValue => $roleLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('users.index', array_merge(request()->only('q'), ['role' => $roleValue]))); ?>" class="px-4 py-2 rounded-xl text-sm font-medium <?php echo e(request('role') === $roleValue ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'bg-white/5 text-[#94a3b8] border border-white/10 hover:bg-white/10'); ?>"><?php echo e($roleLabel); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]" id="users-table-container" data-page="<?php echo e($users->currentPage()); ?>" data-role="<?php echo e(request('role', '')); ?>" data-q="<?php echo e(request('q', '')); ?>">
        <div class="overflow-x-auto text-sm" id="users-table-live">
            <?php echo $__env->make('users.partials.table-content', ['users' => $users], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    const wrapper = document.getElementById('users-table-container');
    const container = document.getElementById('users-table-live');
    if (!wrapper || !container) return;
    const baseUrl = '<?php echo e(route("users.table")); ?>';
    const intervalMs = 5000;

    function refresh() {
        const page = wrapper.getAttribute('data-page') || 1;
        const role = wrapper.getAttribute('data-role') || '';
        const q = wrapper.getAttribute('data-q') || '';
        let url = baseUrl + '?page=' + page + '&_t=' + Date.now();
        if (role) url += '&role=' + encodeURIComponent(role);
        if (q) url += '&q=' + encodeURIComponent(q);
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
        })
        .then(function(r) { return r.text(); })
        .then(function(html) {
            container.innerHTML = html;
            // Mettre à jour data-page depuis la pagination si présente (pour garder la page en sync)
            var newPageInput = container.querySelector('input[name="page"]');
            if (newPageInput && newPageInput.value) wrapper.setAttribute('data-page', newPageInput.value);
        })
        .catch(function() {});
    }

    // Rafraîchir tout de suite au chargement (liste à jour après création d'un utilisateur)
    refresh();
    setInterval(refresh, intervalMs);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/users/index.blade.php ENDPATH**/ ?>