@extends('layouts.app')

@section('title', 'Attribution agences')

@section('content')
<div class="space-y-6 pb-8">
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-[#1e3a8a] to-[#1e40af] border border-white/10 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('equipes.index') }}" class="text-sm text-blue-200/90 hover:text-white mb-2 inline-block">← Retour aux agences</a>
                <h1 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-tight">Attribution agences / sous-agences</h1>
                <p class="text-blue-200/90 text-sm mt-1">Attribue chaque créateur (et chaque membre) à une agence ou une sous-agence. Les noms des agences se gèrent dans <a href="{{ route('equipes.index') }}" class="text-white underline">Agences</a>.</p>
            </div>
        </div>
    </div>

    {{-- Barre de recherche --}}
    <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
        <label for="attribution-search" class="block text-sm font-medium text-[#94a3b8] mb-2">Rechercher par nom ou identifiant</label>
        <input type="text" id="attribution-search" placeholder="Tape un nom pour filtrer…" autocomplete="off"
               class="w-full max-w-md px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white placeholder-[#64748b] focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
        <p class="text-xs text-[#94a3b8] mt-1" id="attribution-search-result"></p>
    </div>

    {{-- Créateurs --}}
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-lg font-semibold text-white">Créateurs ({{ $createurs->count() }})</h2>
            <a href="{{ route('equipes.index') }}" class="text-sm text-sky-400 hover:underline">Gérer les agences (noms)</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-white/5 text-[#94a3b8]">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Créateur</th>
                        <th class="px-4 py-3 font-semibold">Agence actuelle</th>
                        <th class="px-4 py-3 font-semibold">Attribuer à</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="text-white divide-y divide-white/10" id="tbody-createurs">
                    @forelse($createurs as $u)
                    @php
                        $agenceNom = $u->equipe?->nom ?? ($u->createur?->equipe?->nom ?? '');
                        $searchText = strtolower(implode(' ', array_filter([$u->name, $u->username ?? '', $agenceNom])));
                    @endphp
                    <tr class="hover:bg-white/5 attribution-row" data-search="{{ $searchText }}">
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $u->name }}</span>
                            @if($u->username)<span class="text-[#94a3b8] text-xs block">{{ $u->username }}</span>@endif
                        </td>
                        <td class="px-4 py-3 text-[#94a3b8]">{{ $u->equipe?->nom ?? ($u->createur?->equipe?->nom ?? '—') }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('equipes.attribution.assign', $u) }}" method="POST" class="inline-flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="equipe_id" class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-sky-500 min-w-[180px]">
                                    <option value="">— Aucune agence —</option>
                                    @foreach($equipes as $eq)
                                    <option value="{{ $eq->id }}" {{ (old('equipe_id', $u->equipe_id ?? $u->createur?->equipe_id) == $eq->id) ? 'selected' : '' }}>{{ $eq->nom }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-3 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium">Enregistrer</button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('users.edit', $u) }}" class="text-sky-400 hover:underline text-xs">Modifier fiche</a>
                        </td>
                    </tr>
                    @empty
                    <tr class="attribution-row attribution-empty-row">
                        <td colspan="4" class="px-4 py-8 text-center text-[#94a3b8]">Aucun créateur avec fiche. Les créateurs apparaissent après import ou création dans Utilisateurs.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Autres membres (directeurs, manageurs, agents…) --}}
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="px-4 py-3 border-b border-white/10">
            <h2 class="text-lg font-semibold text-white">Directeurs, manageurs, agents ({{ $autresMembres->count() }})</h2>
            <p class="text-xs text-[#94a3b8] mt-1">Attribution à une agence pour limiter leur périmètre (vue équipe).</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-white/5 text-[#94a3b8]">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nom</th>
                        <th class="px-4 py-3 font-semibold">Rôle</th>
                        <th class="px-4 py-3 font-semibold">Agence actuelle</th>
                        <th class="px-4 py-3 font-semibold">Attribuer à</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="text-white divide-y divide-white/10" id="tbody-autres">
                    @forelse($autresMembres as $u)
                    @php
                        $searchTextAutres = strtolower(implode(' ', array_filter([$u->name, $u->username ?? '', $u->getRoleLabel(), $u->equipe?->nom ?? ''])));
                    @endphp
                    <tr class="hover:bg-white/5 attribution-row" data-search="{{ $searchTextAutres }}">
                        <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-[#94a3b8]">{{ $u->getRoleLabel() }}</td>
                        <td class="px-4 py-3 text-[#94a3b8]">{{ $u->equipe?->nom ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('equipes.attribution.assign', $u) }}" method="POST" class="inline-flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="equipe_id" class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-sky-500 min-w-[180px]">
                                    <option value="">— Aucune agence —</option>
                                    @foreach($equipes as $eq)
                                    <option value="{{ $eq->id }}" {{ old('equipe_id', $u->equipe_id) == $eq->id ? 'selected' : '' }}>{{ $eq->nom }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-3 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium">Enregistrer</button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('users.edit', $u) }}" class="text-sky-400 hover:underline text-xs">Modifier</a>
                        </td>
                    </tr>
                    @empty
                    <tr class="attribution-row attribution-empty-row">
                        <td colspan="5" class="px-4 py-8 text-center text-[#94a3b8]">Aucun directeur, manageur ou agent.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var searchInput = document.getElementById('attribution-search');
    var resultLabel = document.getElementById('attribution-search-result');
    if (!searchInput) return;

    function normalize(s) {
        return (s || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
    }

    function updateFilter() {
        var q = normalize(searchInput.value).trim();
        var rows = document.querySelectorAll('.attribution-row');
        var visible = 0;
        rows.forEach(function(tr) {
            if (tr.classList.contains('attribution-empty-row')) {
                tr.style.display = q ? 'none' : '';
                return;
            }
            var text = tr.getAttribute('data-search') || '';
            var show = !q || normalize(text).indexOf(q) !== -1;
            tr.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (resultLabel) {
            if (q.length === 0) {
                resultLabel.textContent = '';
            } else {
                resultLabel.textContent = visible + ' personne(s) affichée(s)';
            }
        }
    }

    searchInput.addEventListener('input', updateFilter);
    searchInput.addEventListener('keyup', updateFilter);
})();
</script>
@endpush
@endsection
