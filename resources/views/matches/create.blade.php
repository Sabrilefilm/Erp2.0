@extends('layouts.app')

@section('title', 'Programmer un match')

@section('content')
<div class="space-y-6 pb-8 max-w-2xl mx-auto">
    {{-- Hero --}}
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-cyan-500/20 via-sky-500/10 to-indigo-500/10 border border-white/10 p-5 md:p-6">
        <div class="flex items-center gap-3 md:gap-4">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                <span class="text-2xl md:text-3xl">📅</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-white">Programmer un match</h1>
                <p class="text-[#94a3b8] text-sm mt-0.5">Choisissez le créateur, la date, l’heure et les infos du créateur adverse.</p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="rounded-xl px-4 py-3 border border-red-500/30 bg-red-500/10 text-red-400 text-sm">
        {{ session('error') }}
    </div>
    @endif

    @if($createurs->isEmpty())
    <div class="rounded-xl px-4 py-3 border border-amber-500/30 bg-amber-500/10 text-amber-400 text-sm">
        Aucun créateur disponible pour votre périmètre. Si vous êtes fondateur d’agence, vérifiez que des créateurs sont bien rattachés à votre équipe.
    </div>
    @endif

    <form action="{{ route('matches.store') }}" method="POST" class="space-y-6" id="match-create-form" data-storage-key="match_last_adverse_{{ auth()->id() }}">
        @csrf
        @php
            $createursJson = $createurs->map(fn($c) => [
                'id' => $c->id,
                'nom' => $c->nom,
                'pseudo' => $c->pseudo_tiktok ?? '',
                'agence' => $c->equipe?->nom ?? '',
                'agent' => $c->agent?->name ?? '',
            ])->values();
            $initialCreateurId = old('createur_id', $defaultCreateurId ?? '');
            $initialCreateur = $createurs->firstWhere('id', $initialCreateurId);
        @endphp

        {{-- Section : Notre créateur --}}
        <div class="rounded-xl border border-white/10 bg-white/[0.02] p-4 md:p-5">
            <h2 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">1</span>
                Notre créateur
            </h2>
            <div class="createur-search-wrap relative" x-data="{
                createurs: {{ Js::from($createursJson) }},
                query: {{ Js::from($initialCreateur ? $initialCreateur->nom : '') }},
                selectedId: {{ Js::from((string)$initialCreateurId) }},
                selectedNom: {{ Js::from($initialCreateur ? $initialCreateur->nom : '') }},
                open: false,
                focusedIndex: 0,
                get filtered() {
                    const q = (this.query || '').trim().toLowerCase();
                    if (!q) return this.createurs;
                    const n = (s) => (s || '').toLowerCase().replace(/\./g, ' ');
                    const qn = q.replace(/\./g, ' ');
                    return this.createurs.filter(c =>
                        (c.nom && n(c.nom).includes(qn) || c.nom.toLowerCase().includes(q)) ||
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
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Créateur (personne de l'agence) *</label>
                <input type="text"
                       x-model="query"
                       @input="onInput()"
                       @focus="open = true"
                       @keydown.arrow-down.prevent="focusNext()"
                       @keydown.arrow-up.prevent="focusPrev()"
                       @keydown.enter.prevent="selectFocused()"
                       placeholder="Tapez le nom, @pseudo ou le nom de l'agent…"
                       autocomplete="off"
                       class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
                <input type="hidden" name="createur_id" :value="selectedId">
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
        </div>

        {{-- Section : Date et heure --}}
        <div class="rounded-xl border border-white/10 bg-white/[0.02] p-4 md:p-5">
            <h2 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">2</span>
                Date et heure
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Date *</label>
                    <input type="date" name="date" required
                           class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30"
                           value="{{ old('date', $defaultDate ?? date('Y-m-d')) }}">
                    @error('date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Heure</label>
                    <input type="time" name="heure"
                           class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30"
                           value="{{ old('heure', $defaultHeure ?? '') }}">
                </div>
            </div>
        </div>

        {{-- Section : Type de match --}}
        <div class="rounded-xl border border-white/10 bg-white/[0.02] p-4 md:p-5">
            <h2 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">3</span>
                Type de match
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Type *</label>
                    <select name="type" id="match-type" required class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
                        @foreach($typeLabels as $value => $label)
                        <option value="{{ $value }}" {{ old('type', $defaultType ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Statut</label>
                    <select name="statut" class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
                        @foreach($statutLabels as $value => $label)
                        <option value="{{ $value }}" {{ old('statut', 'programme') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="anniversaire-pour-qui-wrap" class="mt-4 {{ old('type', $defaultType ?? '') === 'match_anniversaire' ? '' : 'hidden' }}">
                <p class="text-sm text-amber-200/90 bg-amber-500/10 border border-amber-500/30 rounded-lg px-3 py-2">
                    <span class="font-medium">Ce match est pour l'anniversaire du créateur sélectionné ci-dessus.</span> Choisissez « Notre créateur » en section 1 pour indiquer pour qui est l'anniversaire.
                </p>
            </div>
            <div id="niveau-match-wrap" class="mt-4 {{ old('type', $defaultType ?? '') === 'match_off' ? '' : 'hidden' }}">
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Niveau du match officiel *</label>
                <select name="niveau_match" id="niveau_match" class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
                    <option value="">— Choisir —</option>
                    @foreach($niveauLabels as $value => $label)
                    <option value="{{ $value }}" {{ old('niveau_match', $defaultNiveauMatch ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('niveau_match')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Consignes (match officiel)</label>
                <select name="avec_boost" class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30">
                    <option value="0" {{ old('avec_boost', '0') === '0' ? 'selected' : '' }}>Sans boost</option>
                    <option value="1" {{ old('avec_boost') === '1' ? 'selected' : '' }}>Avec boost</option>
                </select>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Raison ou note (optionnel)</label>
                <input type="text" name="raison" placeholder="Ex. Match de la semaine 12"
                       class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30"
                       value="{{ old('raison') }}">
                @error('raison')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Section : Créateur adverse --}}
        <div class="rounded-xl border border-amber-500/25 bg-amber-500/5 p-4 md:p-5">
            <h2 class="text-sm font-semibold text-amber-300/90 mb-1 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">4</span>
                Créateur adverse
            </h2>
            <p class="text-xs text-[#94a3b8] mb-4">Saisissez le @ TikTok : si le créateur est déjà enregistré, les champs se remplissent automatiquement.</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">@ TikTok du créateur adverse *</label>
                    <input type="text" id="createur_adverse_at" name="createur_adverse_at" required
                           placeholder="Ex. @username"
                           class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30"
                           value="{{ old('createur_adverse_at', $defaultCreateurAdverse ?? '') }}"
                           autocomplete="off">
                    @error('createur_adverse_at')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    <p id="lookup-status" class="text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Nom ou agence *</label>
                    <input type="text" name="createur_adverse" id="createur_adverse_nom" required placeholder="Nom du créateur ou agence"
                           class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30"
                           value="{{ old('createur_adverse', $defaultCreateurAdverse ?? '') }}">
                    @error('createur_adverse')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Agent / e du créateur adverse</label>
                    <input type="text" name="createur_adverse_agent" id="createur_adverse_agent" placeholder="Ex. Agent Monsteurs"
                           class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30"
                           value="{{ old('createur_adverse_agent', $defaultCreateurAdverseAgent ?? '') }}">
                    @error('createur_adverse_agent')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Téléphone *</label>
                    <input type="text" name="createur_adverse_numero" required placeholder="Numéro pour contact / litige"
                           class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30"
                           value="{{ old('createur_adverse_numero') }}">
                    @error('createur_adverse_numero')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Autres infos (contact, agence…)</label>
                    <textarea name="createur_adverse_autres" rows="2" placeholder="Optionnel"
                              class="w-full px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] resize-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30">{{ old('createur_adverse_autres') }}</textarea>
                    @error('createur_adverse_autres')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold shadow-lg shadow-cyan-500/20 transition-colors">
                Programmer le match
            </button>
            <a href="{{ route('matches.index') }}" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-[#94a3b8] hover:text-white hover:bg-white/10 text-sm font-medium transition-colors inline-block">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
(function () {
    var typeSelect = document.getElementById('match-type');
    var niveauWrap = document.getElementById('niveau-match-wrap');
    var niveauSelect = document.getElementById('niveau_match');
    var anniversaireWrap = document.getElementById('anniversaire-pour-qui-wrap');
    function toggleTypeOptions() {
        var isOff = typeSelect && typeSelect.value === 'match_off';
        var isAnniversaire = typeSelect && typeSelect.value === 'match_anniversaire';
        if (niveauWrap) niveauWrap.classList.toggle('hidden', !isOff);
        if (niveauSelect) niveauSelect.required = isOff;
        if (anniversaireWrap) anniversaireWrap.classList.toggle('hidden', !isAnniversaire);
    }
    if (typeSelect) typeSelect.addEventListener('change', toggleTypeOptions);
    toggleTypeOptions();

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
        if (at.length < 2) {
            statusEl.classList.add('hidden');
            return;
        }
        showStatus('Recherche…', false);
        fetch(lookupUrl + '?at=' + encodeURIComponent(at))
            .then(function (r) {
                if (r.status === 404) {
                    showStatus('Aucune fiche pour ce @. Complétez les champs ci-dessous.', true);
                    return null;
                }
                return r.json();
            })
            .then(function (data) {
                if (data) {
                    if (nomInput) nomInput.value = data.nom || data.agence || '';
                    if (agentInput) agentInput.value = data.agent || '';
                    if (numInput) numInput.value = data.telephone || '';
                    if (autresInput) autresInput.value = data.autres_infos || '';
                    showStatus('Coordonnées trouvées et remplies.', false);
                }
            })
            .catch(function () {
                showStatus('Erreur de recherche.', true);
            });
    }

    atInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doLookup, 400);
    });
    atInput.addEventListener('blur', function () {
        clearTimeout(debounceTimer);
        if ((atInput.value || '').trim().length >= 2) doLookup();
    });

    var formEl = document.getElementById('match-create-form');
    var storageKey = formEl && formEl.getAttribute('data-storage-key');
    if (storageKey) {
        try {
            var saved = localStorage.getItem(storageKey);
            if (saved) {
                var data = JSON.parse(saved);
                var hasOld = !!(nomInput && nomInput.value) || !!(atInput && atInput.value);
                if (!hasOld) {
                    if (atInput && data.at) atInput.value = data.at;
                    if (nomInput) nomInput.value = (data.nom || data.agence) || '';
                    if (agentInput && data.agent) agentInput.value = data.agent;
                    if (numInput && data.numero) numInput.value = data.numero;
                    if (autresInput && data.autres) autresInput.value = data.autres;
                    if ((atInput && atInput.value) && typeof doLookup === 'function') doLookup();
                }
            }
        } catch (e) {}
        formEl.addEventListener('submit', function () {
            try {
                var payload = {
                    at: (atInput && atInput.value) ? atInput.value.trim() : '',
                    nom: (nomInput && nomInput.value) ? nomInput.value.trim() : '',
                    agent: (agentInput && agentInput.value) ? agentInput.value.trim() : '',
                    numero: (numInput && numInput.value) ? numInput.value.trim() : '',
                    autres: (autresInput && autresInput.value) ? autresInput.value.trim() : ''
                };
                if (payload.at || payload.nom) localStorage.setItem(storageKey, JSON.stringify(payload));
            } catch (e) {}
        });
    }
})();
</script>
@endsection
