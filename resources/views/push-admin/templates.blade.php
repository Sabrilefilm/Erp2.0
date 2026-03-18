@extends('layouts.app')
@section('title', 'Messages predefinis')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <header class="border-b border-white/10 pb-4">
        <a href="{{ route('push-admin.index') }}" class="text-sm text-[#94a3b8] hover:text-white">Retour</a>
        <h1 class="text-xl font-bold text-white mt-2">Messages predefinis</h1>
    </header>
    <section class="rounded-xl border border-white/10 bg-white/5 p-4">
        <h2 class="text-sm font-semibold text-white mb-3">Nouveau modele</h2>
        <form action="{{ route('push-admin.templates.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-[#94a3b8] mb-1">Cle</label>
                    <input type="text" name="key" required maxlength="80" value="{{ old('key') }}" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
                    @error('key')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-[#94a3b8] mb-1">Libelle</label>
                    <input type="text" name="label" required maxlength="255" value="{{ old('label') }}" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm text-[#94a3b8] mb-1">Titre</label>
                <input type="text" name="title" required maxlength="255" value="{{ old('title') }}" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-[#94a3b8] mb-1">Message</label>
                <textarea name="body" rows="2" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2">{{ old('body') }}</textarea>
            </div>
            <label class="flex items-center gap-2"><input type="checkbox" name="active" value="1" checked> <span class="text-sm text-[#94a3b8]">Actif</span></label>
            <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white">Creer</button>
        </form>
    </section>
    @foreach($templates as $t)
    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
        <form action="{{ route('push-admin.templates.update', $t) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <p class="font-mono text-cyan-400 text-sm">{{ $t->key }} - {{ $t->active ? 'Actif' : 'Inactif' }}</p>
            <input type="text" name="label" value="{{ $t->label }}" required class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 text-sm">
            <input type="text" name="title" value="{{ $t->title }}" required class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 text-sm">
            <textarea name="body" rows="2" class="w-full rounded-lg border border-white/20 bg-white/5 text-white px-3 py-2 text-sm">{{ $t->body }}</textarea>
            <label class="flex items-center gap-2"><input type="checkbox" name="active" value="1" {{ $t->active ? 'checked' : '' }}> Actif</label>
            <button type="submit" class="px-3 py-1.5 rounded-lg border border-white/20 text-sm">Mettre a jour</button>
        </form>
    </div>
    @endforeach
</div>
@endsection
