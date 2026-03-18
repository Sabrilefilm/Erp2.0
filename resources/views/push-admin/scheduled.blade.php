@extends('layouts.app')
@section('title', 'Planifier une notification')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <header class="border-b border-white/10 pb-4">
        <a href="{{ route('push-admin.index') }}" class="text-sm text-[#94a3b8] hover:text-white">Retour Notifications Push</a>
        <h1 class="text-xl font-bold text-white mt-2">Planifier un envoi</h1>
        <p class="text-sm text-[#94a3b8] mt-1">La notification sera envoyee a la date et l'heure choisies.</p>
    </header>
    <form action="{{ route('push-admin.scheduled.store') }}" method="POST" class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        @csrf
        <div class="p-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Cible</label>
                <select name="target_type" id="target_type" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
                    <option value="all">Tous</option>
                    <option value="role">Un role</option>
                    <option value="user">Un utilisateur</option>
                </select>
            </div>
            <div id="target_value_wrap" style="display: none;">
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Selection</label>
                <select name="target_value" id="target_value_role" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 hidden">
                    @foreach($roles as $role => $label)<option value="{{ $role }}">{{ $label }}</option>@endforeach
                </select>
                <select name="target_value" id="target_value_user" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 hidden">
                    @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Titre</label>
                <input type="text" name="title" required maxlength="255" value="{{ old('title') }}" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#94a3b8] mb-1">Message</label>
                <textarea name="body" rows="3" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">{{ old('body') }}</textarea>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-[#94a3b8] mb-1">Date</label>
                    <input type="date" name="send_at_date" required value="{{ old('send_at_date') }}" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#94a3b8] mb-1">Heure</label>
                    <input type="time" name="send_at_time" required value="{{ old('send_at_time', '20:00') }}" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
                </div>
            </div>
            @error('send_at_date')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        </div>
        <div class="px-4 py-3 border-t border-white/10"><button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white hover:bg-cyan-500">Planifier</button></div>
    </form>
    <script>
    (function(){var t=document.getElementById('target_type'),w=document.getElementById('target_value_wrap'),r=document.getElementById('target_value_role'),u=document.getElementById('target_value_user');
    function up(){var v=t.value;w.style.display=(v==='role'||v==='user')?'block':'none';r.classList.toggle('hidden',v!=='role');u.classList.toggle('hidden',v!=='user');r.name=(v==='role')?'target_value':'';u.name=(v==='user')?'target_value':'';}
    t.addEventListener('change',up);up();})();
    </script>
    @if($scheduled->isNotEmpty())
    <section class="rounded-xl border border-white/10 bg-white/5 overflow-hidden">
        <h2 class="px-4 py-3 border-b border-white/10 text-sm font-semibold text-white">En attente</h2>
        <ul class="divide-y divide-white/10">
            @foreach($scheduled as $s)
            <li class="px-4 py-3">
                <p class="text-sm text-white font-medium">{{ $s->title }}</p>
                <p class="text-xs text-[#94a3b8]">{{ $s->send_at->format('d/m/Y H:i') }} - {{ $s->target_type }}{{ $s->target_value ? ': '.$s->target_value : '' }}</p>
            </li>
            @endforeach
        </ul>
    </section>
    @endif
</div>
@endsection
