@extends('layouts.app')

@section('title', 'Agences')

@section('content')
<div class="space-y-6 pb-8">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e3a8a] to-[#1e40af] border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-tight">Agences</h1>
                <p class="text-blue-200/90 text-sm mt-1">Crée des agences et sous-agences, donne-leur un nom (ex. « Agence Paris », « Sous-agence Lyon »). Puis attribue les créateurs et membres depuis la page <strong>Attribution agences</strong>.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('equipes.attribution') }}" class="inline-flex px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white font-medium border border-white/20">
                    Attribution agences
                </a>
                <a href="{{ route('equipes.create') }}" class="inline-flex px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-medium">
                    + Créer une agence
                </a>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-white/5 text-[#94a3b8]">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nom</th>
                        <th class="px-4 py-3 font-semibold">Manager</th>
                        <th class="px-4 py-3 font-semibold">Membres</th>
                        <th class="px-4 py-3 font-semibold">Créateurs (fiches)</th>
                        <th class="px-4 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-white divide-y divide-white/10">
                    @forelse($equipes as $equipe)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3 font-medium">{{ $equipe->nom }}</td>
                        <td class="px-4 py-3">{{ $equipe->manager?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $equipe->membres_count }}</td>
                        <td class="px-4 py-3">{{ $equipe->createurs_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('equipes.membres', $equipe) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sky-600/20 text-sky-400 hover:bg-sky-600/30 font-medium text-sm mr-2">Attribuer / Gérer</a>
                            <a href="{{ route('equipes.edit', $equipe) }}" class="text-sky-400 hover:underline mr-3">Modifier</a>
                            <form action="{{ route('equipes.destroy', $equipe) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette agence ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-400 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-[#94a3b8]">Aucune agence. Créez-en une.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
