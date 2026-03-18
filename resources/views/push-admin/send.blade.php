@extends('layouts.app')

@section('title', 'Envoyer une notification push')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <header class="border-b border-white/10 pb-4">
        <a href="{{ route('push-admin.index') }}" class="text-sm text-[#94a3b8] hover:text-white">← Notifications Push</a>
        <h1 class="text-xl font-bold text-white mt-2">Envoyer une notification</h1>
        <p class="text-sm text-[#94a3b8] mt-1">Choisissez la cible et le message.</p>
    </header>

    <form action="{{ route('push-admin.send.store') }}" method="POST" class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        @csrf
        <div class="p-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Cible</label>
                <select name="target_type" id="target_type" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 focus:ring-2 focus:ring-cyan-500">
                    <option value="all">Tous les utilisateurs</option>
                    <option value="role">Un rôle</option>
                    <option value="user">Un utilisateur</option>
                </select>
            </div>
            <div id="target_value_wrap" style="display: none;">
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Sélection</label>
                <select name="target_value" id="target_value_role" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 hidden">
                    @foreach($roles as $role => $label)
                    <option value="{{ $role }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select name="target_value" id="target_value_user" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 hidden">
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Modèle (optionnel)</label>
                <select name="template_key" id="template_key" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
                    <option value="">— Message personnalisé —</option>
                    @foreach($templates as $t)
                    <option value="{{ $t->key }}" data-title="{{ e($t->title) }}" data-body="{{ e($t->body ?? '') }}">{{ $t->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Titre <span class="text-red-400">*</span></label>
                <input type="text" name="title" id="title" required maxlength="255" value="{{ old('title') }}" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 focus:ring-2 focus:ring-cyan-500">
                @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Message</label>
                <textarea name="body" id="body" rows="3" maxlength="5000" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 focus:ring-2 focus:ring-cyan-500">{{ old('body') }}</textarea>
                @error('body')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="px-4 py-3 border-t border-white/10 flex justify-end gap-2">
            <a href="{{ route('push-admin.index') }}" class="px-4 py-2 rounded-lg border border-white/20 text-[#94a3b8] hover:bg-white/5">Annuler</a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white hover:bg-cyan-500">Envoyer</button>
        </div>
    </form>
</div>
<script>
(function() {
    var targetType = document.getElementById('target_type');
    var wrap = document.getElementById('target_value_wrap');
    var roleSelect = document.getElementById('target_value_role');
    var userSelect = document.getElementById('target_value_user');
    var templateSelect = document.getElementById('template_key');
    var titleInput = document.getElementById('title');
    var bodyInput = document.getElementById('body');
    function updateTarget() {
        var v = targetType.value;
        wrap.style.display = (v === 'role' || v === 'user') ? 'block' : 'none';
        roleSelect.classList.toggle('hidden', v !== 'role');
        userSelect.classList.toggle('hidden', v !== 'user');
        roleSelect.name = (v === 'role') ? 'target_value' : '';
        userSelect.name = (v === 'user') ? 'target_value' : '';
    }
    targetType.addEventListener('change', updateTarget);
    updateTarget();
    templateSelect.addEventListener('change', function() {
        var opt = templateSelect.options[templateSelect.selectedIndex];
        if (opt.value && opt.dataset.title) {
            titleInput.value = opt.dataset.title;
            bodyInput.value = opt.dataset.body || '';
        }
    });
})();
</script>
@endsection
