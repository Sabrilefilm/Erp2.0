@extends('layouts.app')

@section('title', 'Attribution – ' . $equipe->nom)

@section('content')
<div class="space-y-6 pb-8">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e3a8a] to-[#1e40af] border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('equipes.index') }}" class="text-sm text-blue-200/90 hover:text-white mb-2 inline-block">← Retour aux agences</a>
                <h1 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-tight">Attribution – {{ $equipe->nom }}</h1>
                <p class="text-blue-200/90 text-sm mt-1">Attribuer des membres à cette agence, modifier l'agence ou le rôle de chacun.</p>
            </div>
        </div>
    </div>

    {{-- Attribuer une personne à cette agence --}}
    @if($usersHorsAgence->isNotEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-6">
        <h2 class="text-lg font-semibold text-white mb-3">Attribuer une personne à cette agence</h2>
        <form action="{{ route('equipes.attribuer', $equipe) }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="min-w-[200px] flex-1">
                <label for="user_id" class="block text-sm font-medium text-[#94a3b8] mb-1">Personne</label>
                <select name="user_id" id="user_id" required class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    <option value="">— Choisir —</option>
                    @foreach($usersHorsAgence as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->getRoleLabel() }}){{ $u->equipe ? ' – ' . $u->equipe->nom : '' }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-medium whitespace-nowrap">Attribuer à cette agence</button>
        </form>
    </div>
    @endif

    {{-- Liste des membres --}}
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="px-4 py-3 border-b border-white/10">
            <h2 class="text-lg font-semibold text-white">Membres de cette agence ({{ $membres->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-white/5 text-[#94a3b8]">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nom</th>
                        <th class="px-4 py-3 font-semibold">Rôle</th>
                        <th class="px-4 py-3 font-semibold">Changer d'agence</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-white divide-y divide-white/10">
                    @forelse($membres as $u)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-[#94a3b8]">{{ $u->getRoleLabel() }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('equipes.changer-agence', [$equipe, $u]) }}" method="POST" class="inline-flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="nouvelle_equipe_id" class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-sky-500">
                                    <option value="">— Retirer (aucune agence) —</option>
                                    @foreach($autresEquipes as $eq)
                                    <option value="{{ $eq->id }}">{{ $eq->nom }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-sky-400 hover:text-sky-300 text-sm font-medium">Appliquer</button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.edit', $u) }}" class="text-sky-400 hover:underline">Modifier rôle / agence</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-[#94a3b8]">Aucun membre. Utilise le formulaire ci‑dessus pour en attribuer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($createursSansUser->isNotEmpty())
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="px-4 py-3 border-b border-white/10">
            <h2 class="text-lg font-semibold text-white">Fiches créateurs (sans compte) dans cette agence</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-white/5 text-[#94a3b8]">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nom</th>
                        <th class="px-4 py-3 font-semibold">Agent</th>
                    </tr>
                </thead>
                <tbody class="text-white divide-y divide-white/10">
                    @foreach($createursSansUser as $c)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3">{{ $c->nom }}</td>
                        <td class="px-4 py-3 text-[#94a3b8]">{{ $c->agent?->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
