@extends('layouts.app')

@section('title', 'Modifier le match')

@section('content')
<div class="space-y-6 max-w-xl">
    <h1 class="text-2xl font-bold bg-gradient-to-r from-neon-blue to-neon-purple bg-clip-text text-transparent">Modifier le match</h1>

    @if(session('error'))
    <div class="ultra-card rounded-lg px-4 py-3 border border-red-500/30 bg-red-500/10 text-red-400 text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="ultra-card rounded-xl p-5 border border-white/10">
        <form action="{{ route('matches.update', $match) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            @php
                $createursEditJson = collect($createurs)->map(fn($c) => [
                    'id' => $c->id,
                    'nom' => $c->nom,
                    'pseudo' => $c->pseudo_tiktok ?? '',
                    'agence' => $c->equipe?->nom ?? '',
                    'agent' => $c->agent?->name ?? '',
                ])->values();
                $editCreateurId = (string) old('createur_id', $match->createur_id);
                $editInitialCreateur = collect($createurs)->firstWhere('id', (int) $editCreateurId);
            @endphp
            <div class="createur-search-wrap relative" x-data="{
                createurs: {{ Js::from($createursEditJson) }},
                query: {{ Js::from($editInitialCreateur ? $editInitialCreateur->nom : '') }},
                selectedId: {{ Js::from($editCreateurId) }},
                selectedNom: {{ Js::from($editInitialCreateur ? $editInitialCreateur->nom : '') }},
                open: false,
                focusedIndex: 0,
                get filtered() {
                    const q = (this.query || '').trim().toLowerCase();
                    if (!q) return this.createurs;
                    const n = (s) => (s || '').toLowerCase().replace(/\./g, ' ');
                    const qn = q.replace(/\./g, ' ');
                    return this.createurs.filter(c =>
                        (c.nom && (n(c.nom).includes(qn) || c.nom.toLowerCase().includes(q))) ||
                        (c.pseudo && (n(c.pseudo).includes(qn) || c.pseudo.toLowerCase().includes(q))) ||
                        (c.agent && c.agent.toLowerCase().includes(q)) ||
                        (c.agence && c.agence.toLowerCase().includes(q))
                    );
                },
                onInput() {
                    this.open = true;
                    this.focusedIndex = 0;
                    const match = this.createurs.find(c => c.nom && c.nom.toLowerCase() === (this.query || '').trim().toLowerCase());
                    if (match) { this.selectedId = String(match.id); this.selectedNom = match.nom; }
                    else { this.selectedId = ''; this.selectedNom = ''; }
                },
                focusNext() { const list = this.filtered; if (list.length) this.focusedIndex = (this.focusedIndex + 1) % list.length; },
                focusPrev() { const list = this.filtered; if (list.length) this.focusedIndex = (this.focusedIndex - 1 + list.length) % list.length; },
                selectFocused() { const list = this.filtered; if (list[this.focusedIndex]) this.select(list[this.focusedIndex]); },
                select(c) {
                    this.selectedId = String(c.id);
                    this.selectedNom = c.nom;
                    this.query = c.nom;
                    this.open = false;
                }
            }">
                <label class="block text-xs text-[#6b7a9f] mb-1">Créateur <span class="text-red-400">*</span></label>
                <input type="text"
                       x-model="query"
                       @input="onInput()"
                       @focus="open = true"
                       @keydown.arrow-down.prevent="focusNext()"
                       @keydown.arrow-up.prevent="focusPrev()"
                       @keydown.enter.prevent="selectFocused()"
                       placeholder="Tapez le nom, @pseudo ou le nom de l'agent…"
                       autocomplete="off"
                       class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                <input type="hidden" name="createur_id" :value="selectedId" required>
                <div x-show="open && filtered.length > 0"
                     x-transition
                     class="absolute left-0 right-0 mt-1 max-h-80 overflow-y-auto rounded-lg border border-white/20 bg-[#1e293b] shadow-xl z-20"
                     @click.outside="open = false">
                    <template x-for="(c, i) in filtered" :key="c.id">
                        <button type="button"
                                class="w-full text-left px-3 py-2.5 text-sm text-white hover:bg-white/10 border-b border-white/5 last:border-0 transition-colors"
                                :class="{ 'bg-cyan-500/20': i === focusedIndex }"
                                @click="select(c)">
                            <span x-text="c.nom" class="font-medium"></span>
                            <span x-show="c.pseudo" class="text-white/50 text-xs ml-1" x-text="'@' + c.pseudo"></span>
                            <div x-show="c.agence || c.agent" class="text-[11px] text-white/50 mt-0.5" x-text="(c.agence || '') + (c.agent ? ' · ' + c.agent : '')"></div>
                        </button>
                    </template>
                </div>
                <p x-show="selectedId && selectedNom" class="text-xs text-emerald-400/90 mt-1.5">Sélectionné : <span x-text="selectedNom"></span></p>
                @error('createur_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Date <span class="text-red-400">*</span></label>
                    <input type="date" name="date" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('date', $match->date?->format('Y-m-d')) }}">
                    @error('date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Heure</label>
                    <input type="time" name="heure" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('heure', $match->heure ? substr($match->heure, 0, 5) : '') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Type de match <span class="text-red-400">*</span></label>
                    <select name="type" id="match-type" required class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                        @foreach($typeLabels as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $match->type) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Statut</label>
                    <select name="statut" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                        @foreach($statutLabels as $value => $label)
                        <option value="{{ $value }}" @selected(old('statut', $match->statut ?? 'programme') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="niveau-match-wrap" class="{{ old('type', $match->type) === 'match_off' ? '' : 'hidden' }}">
                <label class="block text-xs text-[#6b7a9f] mb-1">Niveau du match * <span class="text-white/50 font-normal">(Match officiel)</span></label>
                <select name="niveau_match" id="niveau_match" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                    <option value="">— Choisir —</option>
                    @foreach($niveauLabels as $value => $label)
                    <option value="{{ $value }}" @selected(old('niveau_match', $match->niveau_match) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('niveau_match')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Consignes (rappel pour le créateur)</label>
                <select name="avec_boost" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm">
                    <option value="0" @selected((int)old('avec_boost', $match->avec_boost) === 0)>Match officiel sans boost</option>
                    <option value="1" @selected((int)old('avec_boost', $match->avec_boost) === 1)>Match officiel avec boost</option>
                </select>
            </div>
            {{-- Créateur adverse : @ + coordonnées obligatoires --}}
            <div class="rounded-xl border border-amber-500/30 bg-amber-500/5 p-4 space-y-4">
                <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Créateur adverse — coordonnées</p>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">@ TikTok <span class="text-red-400">*</span></label>
                    <input type="text" id="createur_adverse_at" name="createur_adverse_at" required placeholder="Ex. @username"
                           class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm"
                           value="{{ old('createur_adverse_at', $match->createur_adverse_at ?? '') }}">
                    @error('createur_adverse_at')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    <p id="lookup-status" class="text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Nom ou agence <span class="text-red-400">*</span></label>
                    <input type="text" name="createur_adverse" required placeholder="Nom ou agence"
                           class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm"
                           value="{{ old('createur_adverse', $match->createur_adverse) }}">
                    @error('createur_adverse')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Nom de l'agent / e</label>
                    <input type="text" name="createur_adverse_agent" placeholder="Ex. Agent Monsteurs"
                           class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm"
                           value="{{ old('createur_adverse_agent', $match->createur_adverse_agent ?? '') }}">
                    @error('createur_adverse_agent')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Téléphone <span class="text-red-400">*</span></label>
                    <input type="text" name="createur_adverse_numero" required placeholder="Téléphone"
                           class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm"
                           value="{{ old('createur_adverse_numero', $match->createur_adverse_numero) }}">
                    @error('createur_adverse_numero')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs text-[#6b7a9f] mb-1">Autres infos</label>
                    <textarea name="createur_adverse_autres" rows="2" placeholder="Optionnel"
                              class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm resize-none">{{ old('createur_adverse_autres', $match->createur_adverse_autres) }}</textarea>
                    @error('createur_adverse_autres')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-xs text-[#6b7a9f] mb-1">Raison</label>
                <input type="text" name="raison" placeholder="Optionnel" class="ultra-input w-full px-3 py-2 rounded-lg text-white text-sm" value="{{ old('raison', $match->raison) }}">
                @error('raison')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-lg text-sm font-semibold"><span>Enregistrer</span></button>
                <a href="{{ route('matches.index') }}" class="ultra-input px-4 py-2 rounded-lg text-sm font-medium text-[#b0bee3] hover:text-white transition-colors inline-block">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var typeSelect = document.getElementById('match-type');
    var niveauWrap = document.getElementById('niveau-match-wrap');
    var niveauSelect = document.getElementById('niveau_match');
    function toggleNiveau() {
        var isOff = typeSelect && typeSelect.value === 'match_off';
        if (niveauWrap) niveauWrap.classList.toggle('hidden', !isOff);
        if (niveauSelect) niveauSelect.required = isOff;
    }
    if (typeSelect) typeSelect.addEventListener('change', toggleNiveau);
    toggleNiveau();

    var atInput = document.getElementById('createur_adverse_at');
    var statusEl = document.getElementById('lookup-status');
    var form = atInput && atInput.closest('form');
    if (!form) return;
    var nomInput = form.querySelector('input[name="createur_adverse"]');
    var agentInput = form.querySelector('input[name="createur_adverse_agent"]');
    var numInput = form.querySelector('input[name="createur_adverse_numero"]');
    var autresInput = form.querySelector('textarea[name="createur_adverse_autres"]');
    var debounceTimer;
    var lookupUrl = '{{ route("matches.createur-adverse.lookup") }}';
    function showStatus(msg, isError) {
        statusEl.textContent = msg;
        statusEl.classList.remove('hidden', 'text-red-400', 'text-green-400');
        statusEl.classList.add(isError ? 'text-red-400' : 'text-green-400');
    }
    function doLookup() {
        var raw = (atInput.value || '').trim();
        var at = raw.replace(/^@+/, '');
        if (at.length < 2) { statusEl.classList.add('hidden'); return; }
        showStatus('Recherche…', false);
        fetch(lookupUrl + '?at=' + encodeURIComponent(at))
            .then(function (r) { return r.status === 404 ? null : r.json(); })
            .then(function (data) {
                if (data) {
                    if (nomInput) nomInput.value = data.nom || data.agence || '';
                    if (agentInput) agentInput.value = data.agent || '';
                    if (numInput) numInput.value = data.telephone || '';
                    if (autresInput) autresInput.value = data.autres_infos || '';
                    showStatus('Coordonnées remplies.', false);
                } else { statusEl.classList.add('hidden'); }
            })
            .catch(function () { showStatus('Erreur.', true); });
    }
    atInput.addEventListener('input', function () { clearTimeout(debounceTimer); debounceTimer = setTimeout(doLookup, 400); });
    atInput.addEventListener('blur', function () { clearTimeout(debounceTimer); if ((atInput.value || '').trim().length >= 2) doLookup(); });
})();
</script>
@endsection
