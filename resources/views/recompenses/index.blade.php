@extends('layouts.app')

@section('title', 'Récompenses')

@section('content')
<div class="space-y-6 pb-8">
    {{-- Hero : titre + description + bloc « À réceptionner » en mieux --}}
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-emerald-500/20 via-teal-500/10 to-neon-green/5 border border-white/10 p-4 md:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 md:gap-6">
            <div class="flex items-center gap-3 md:gap-4 min-w-0">
                <div class="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-neon-green/20 flex items-center justify-center shrink-0 shadow-lg shadow-neon-green/20">
                    <span class="text-2xl md:text-3xl">🏆</span>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-white">Récompenses</h1>
                    <p class="text-[#94a3b8] text-xs md:text-sm mt-0.5">@if(auth()->user()->canAttribuerOuRecupererRecompense()) Attribuez une récompense et consultez les informations (liste, factures, codes). @else Consultez les récompenses de votre périmètre. La facture est disponible quelques secondes après avoir choisi le mode de réception. Seul le fondateur peut attribuer une récompense. @endif</p>
                </div>
            </div>
            {{-- À réceptionner (pour vous) — version épurée --}}
            <div class="shrink-0">
                <div class="rounded-xl border border-neon-green/30 bg-neon-green/10 px-4 py-3 text-center min-w-[160px]">
                    <p class="text-[10px] font-semibold text-neon-green/90 uppercase tracking-wider">À réceptionner (pour vous)</p>
                    <p class="text-[11px] text-[#94a3b8] mt-1">Solde disponible</p>
                    @if($montantAReceptionner > 0)
                    <p class="text-lg font-bold text-neon-green tabular-nums mt-0.5">{{ number_format($montantAReceptionner, 2, ',', ' ') }} €</p>
                    <p class="text-[10px] text-[#94a3b8] mt-1">Choisissez comment recevoir ci-dessous</p>
                    @else
                    <p class="text-lg font-bold text-white/80 tabular-nums mt-0.5">0,00 €</p>
                    <p class="text-[10px] text-amber-300/80 mt-1">Bientôt disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Messages flash (success, error, info, errors) affichés par le layout app.blade.php — pas de doublon ici --}}

    @if($recompensesEnAttenteChoix->isNotEmpty())
    @php $firstRecompenseId = $recompensesEnAttenteChoix->first()->id; $hasMultiple = $recompensesEnAttenteChoix->count() > 1; @endphp
    {{-- Une seule instruction + accordéon : une récompense à la fois pour éviter le doublon --}}
    <p class="text-white font-semibold text-sm md:text-base mb-3 md:mb-4">Choisissez une option ci-dessous (virement, TikTok ou carte cadeau), puis cliquez Confirmer.</p>
    @if($recompensesEnAttenteChoix->count() > 1)
    <p class="text-[#94a3b8] text-sm mb-2">Total à réceptionner : <strong class="text-neon-green">{{ number_format($montantAReceptionner, 2, ',', ' ') }} €</strong> ({{ $recompensesEnAttenteChoix->count() }} récompenses — les montants s’accumulent).</p>
    @endif
    <div class="space-y-2 recompense-choix-accordion" x-data="{ openId: {{ $firstRecompenseId }} }">
    @foreach($recompensesEnAttenteChoix as $rAttente)
    <div class="rounded-xl md:rounded-2xl border border-white/10 bg-white/[0.03] overflow-hidden recompense-choix-block">
        @if($hasMultiple)
        <button type="button" class="w-full px-3 py-3 md:px-4 md:py-3 flex items-center justify-between gap-2 text-left hover:bg-white/5 transition-colors recompense-choix-header" @click="openId = {{ $rAttente->id }}" x-show="openId !== {{ $rAttente->id }}" x-transition>
            <span class="font-semibold text-neon-green tabular-nums">{{ number_format($rAttente->montant, 2, ',', ' ') }} €</span>
            @if($rAttente->raison)<span class="text-white/70 text-sm truncate">{{ $rAttente->raison }}</span>@endif
            <span class="text-[#64748b] text-xs flex-shrink-0">Choisir le mode de réception →</span>
        </button>
        @endif
        <div class="recompense-choix-body" x-show="openId === {{ $rAttente->id }}" x-transition>
        <form action="{{ route('recompenses.choisir-type', $rAttente) }}" method="POST" class="p-3 md:p-5 lg:p-6 space-y-4 md:space-y-6 recompense-choix-form {{ $hasMultiple ? 'border-t border-white/10' : '' }}" data-recompense-id="{{ $rAttente->id }}">
            @csrf
            {{-- Radios pour le type : le navigateur envoie toujours la valeur cochée --}}
            @php $oldType = old('type', ''); @endphp
            <div class="hidden" aria-hidden="true">
                <input type="radio" name="type" value="virement" id="type_virement_{{ $rAttente->id }}" class="recompense-type-radio" {{ $oldType === 'virement' ? 'checked' : '' }}>
                <input type="radio" name="type" value="tiktok" id="type_tiktok_{{ $rAttente->id }}" class="recompense-type-radio" {{ $oldType === 'tiktok' ? 'checked' : '' }}>
                <input type="radio" name="type" value="carte_cadeau" id="type_carte_cadeau_{{ $rAttente->id }}" class="recompense-type-radio" {{ $oldType === 'carte_cadeau' ? 'checked' : '' }}>
            </div>
            <div>
                <p class="text-xs md:text-sm font-semibold text-white mb-2 md:mb-3">Choisir :</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 md:gap-4">
                    <button type="button" data-recompense-type="virement" class="recompense-choix-card flex flex-col items-center justify-center gap-1 min-h-[100px] md:min-h-[140px] p-3 md:p-5 rounded-xl md:rounded-2xl border-2 border-white/20 bg-blue-500/10 hover:border-blue-400 hover:bg-blue-500/20 active:scale-[0.98] transition-all cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a]">
                        <span class="text-2xl md:text-3xl" aria-hidden="true">🏦</span>
                        <span class="font-bold text-white text-sm md:text-base">Virement bancaire</span>
                        <span class="text-[10px] md:text-xs text-[#94a3b8] hidden sm:inline">Sur mon compte</span>
                        <span class="text-[9px] md:text-[10px] uppercase tracking-wider text-blue-300/80">Choisir</span>
                    </button>
                    <button type="button" data-recompense-type="tiktok" class="recompense-choix-card flex flex-col items-center justify-center gap-1 min-h-[100px] md:min-h-[140px] p-3 md:p-5 rounded-xl md:rounded-2xl border-2 border-white/20 bg-black/30 hover:border-pink-400 hover:bg-pink-500/20 active:scale-[0.98] transition-all cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a]">
                        <span class="text-2xl md:text-3xl" aria-hidden="true">🎬</span>
                        <span class="font-bold text-white text-sm md:text-base">Cadeau TikTok</span>
                        <span class="text-[10px] md:text-xs text-[#94a3b8] hidden sm:inline">50 % en cadeaux live</span>
                        <span class="text-[9px] md:text-[10px] uppercase tracking-wider text-pink-300/80">Choisir</span>
                    </button>
                    <button type="button" data-recompense-type="carte_cadeau" class="recompense-choix-card flex flex-col items-center justify-center gap-1 min-h-[100px] md:min-h-[140px] p-3 md:p-5 rounded-xl md:rounded-2xl border-2 border-white/20 bg-amber-500/10 hover:border-amber-400 hover:bg-amber-500/20 active:scale-[0.98] transition-all cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a]">
                        <span class="text-2xl md:text-3xl" aria-hidden="true">🎟️</span>
                        <span class="font-bold text-white text-sm md:text-base">Carte cadeau</span>
                        <span class="text-[10px] md:text-xs text-[#94a3b8] hidden sm:inline">Code sur facture · Valable 1 fois, expire sous 1 an</span>
                        <span class="text-[9px] md:text-[10px] uppercase tracking-wider text-amber-300/90">Choisir</span>
                    </button>
                </div>
            </div>

            {{-- Étape 2 : Formulaire selon le choix (affiché uniquement si type choisi) --}}
            <div class="recompense-panel recompense-panel-virement hidden rounded-xl border border-blue-500/30 bg-blue-500/10 p-4 space-y-4">
                <p class="text-sm font-semibold text-white">Coordonnées bancaires.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-white/80 mb-1">Nom *</label>
                        <input type="text" name="rib_nom" value="{{ old('rib_nom') }}" class="w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" maxlength="120" placeholder="Dupont">
                    </div>
                    <div>
                        <label class="block text-xs text-white/80 mb-1">Prénom *</label>
                        <input type="text" name="rib_prenom" value="{{ old('rib_prenom') }}" class="w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" maxlength="120" placeholder="Jean">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-white/80 mb-1">IBAN * (la banque se remplit automatiquement)</label>
                    <input type="text" name="rib_iban" id="rib_iban" value="{{ old('rib_iban') }}" class="recompense-rib-iban w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm font-mono" maxlength="50" placeholder="FR76 1234 5678 9012 3456 7890 123" autocomplete="off">
                    <p class="text-[10px] text-blue-200/70 mt-1 recompense-iban-status" aria-live="polite"></p>
                </div>
                <div>
                    <label class="block text-xs text-white/80 mb-1">Nom de la banque * (rempli automatiquement depuis l’IBAN, modifiable)</label>
                    <input type="text" name="rib_banque" id="rib_banque" value="{{ old('rib_banque') }}" class="recompense-rib-banque w-full px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" maxlength="120" placeholder="Saisissez l’IBAN ci-dessus pour trouver la banque" list="liste-banques" autocomplete="organization">
                    <datalist id="liste-banques">
                        <option value="BNP Paribas">
                        <option value="Société Générale">
                        <option value="Crédit Agricole">
                        <option value="CIC - Crédit Industriel et Commercial">
                        <option value="Crédit Mutuel">
                        <option value="Caisse d'Épargne">
                        <option value="Banque Populaire">
                        <option value="La Banque Postale">
                        <option value="HSBC France">
                        <option value="Boursobank (ex-Boursorama)">
                        <option value="Hello bank!">
                        <option value="Fortuneo">
                        <option value="ING">
                        <option value="AXA Banque">
                        <option value="Crédit du Nord">
                        <option value="LCL">
                        <option value="Monabanq">
                        <option value="N26">
                        <option value="Revolut">
                        <option value="Orange Bank">
                        <option value="Nickel">
                        <option value="Compte Nickel">
                        <option value="Qonto">
                        <option value="Shine">
                        <option value="Monese">
                        <option value="BforBank">
                        <option value="CIC Banque">
                        <option value="Banque Kolb">
                        <option value="Crédit Coopératif">
                        <option value="Banque Palatine">
                        <option value="BRED">
                        <option value="BPI France">
                        <option value="Caisse d'Épargne Île-de-France">
                        <option value="Crédit Mutuel Arkéa">
                        <option value="Crédit Mutuel Alliance Fédérale">
                        <option value="Banque Populaire du Nord">
                        <option value="Banque Populaire Rives de Paris">
                        <option value="CIC Est">
                        <option value="CIC Ouest">
                        <option value="CIC Lyonnaise de Banque">
                        <option value="CIC Nord Ouest">
                        <option value="CIC Sud-Ouest">
                        <option value="CIC Bourgogne Franche-Comté">
                    </datalist>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="rib_confirme" value="1" class="rounded accent-neon-green" {{ old('rib_confirme') ? 'checked' : '' }}>
                    <span class="text-sm text-white/90">J’atteste que ces informations sont exactes *</span>
                </label>
                @error('rib_confirme')
                <p class="text-amber-400 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs font-normal text-white/80 mt-1">Facture disponible *</p>
            </div>

            <div class="recompense-panel recompense-panel-tiktok hidden rounded-xl border border-pink-500/30 bg-black/20 p-4 flex flex-wrap gap-4">
                <p class="text-sm font-semibold text-white w-full">Date et heure du cadeau en live.</p>
                <div>
                    <label class="block text-xs text-white/80 mb-1">Date * (entre aujourd’hui et 2 mois)</label>
                    <input type="date" name="date_cadeau_tiktok" value="{{ old('date_cadeau_tiktok') }}" class="px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm" min="{{ now()->format('Y-m-d') }}" max="{{ now()->addMonths(2)->format('Y-m-d') }}" required>
                    @error('date_cadeau_tiktok')
                    <p class="text-amber-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs text-white/80 mb-1">Heure *</label>
                    <input type="time" name="heure_cadeau_tiktok" value="{{ old('heure_cadeau_tiktok') }}" class="px-3 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white text-sm">
                    @error('heure_cadeau_tiktok')
                    <p class="text-amber-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <p class="text-xs font-normal text-white/80 w-full mt-1">Facture disponible *</p>
            </div>

            @if($errors->has('type_carte_cadeau') || $errors->has('type'))
            <div class="rounded-xl bg-red-500/20 border border-red-400/50 text-red-200 text-sm px-4 py-3 flex items-start gap-2" role="alert">
                <span class="text-lg shrink-0" aria-hidden="true">⚠️</span>
                <p class="font-medium">{{ $errors->first('type_carte_cadeau') ?: $errors->first('type') }}</p>
            </div>
            @endif
            <div class="pt-4 recompense-submit-area">
                <button type="submit" class="recompense-submit-btn inline-flex items-center justify-center gap-2 px-10 py-4 rounded-2xl bg-neon-green text-white font-bold text-base border-0 shadow-lg shadow-neon-green/40 opacity-70 hover:opacity-100 hover:brightness-110 hover:shadow-xl active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a] cursor-pointer">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Confirmer
                </button>
                <p class="text-xs text-[#64748b] mt-2 recompense-hint hidden">Choisissez une option ci-dessous (virement, TikTok ou carte cadeau), puis cliquez Confirmer.</p>
            </div>
        </form>

            {{-- Formulaire dédié carte cadeau --}}
            @php $montantsDisponibles = array_filter(\App\Models\Recompense::MONTANTS_CARTE_CADEAU, fn($m) => $m <= (float) $rAttente->montant); @endphp
            <div class="recompense-panel recompense-panel-carte_cadeau hidden rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 space-y-3 mt-4">
                <p class="text-sm font-semibold text-amber-100">Choisissez votre carte cadeau.</p>
                <p class="text-xs text-amber-200/80">Solde disponible : <strong>{{ number_format($rAttente->montant, 2, ',', ' ') }} €</strong>. Le code est valable une seule fois dès utilisation, et expire 1 an à compter de la date d'achat.</p>
                <form action="{{ route('recompenses.choisir-type', $rAttente) }}" method="POST" class="space-y-3 formulaire-carte-cadeau" data-montant="{{ $rAttente->montant }}">
                    @csrf
                    <input type="hidden" name="type" value="carte_cadeau">
                    <div>
                        <label class="block text-xs text-amber-100/90 mb-1.5">Type de carte cadeau *</label>
                        <select name="type_carte_cadeau" required class="w-full max-w-md px-4 py-3 rounded-xl bg-white/10 border text-amber-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 {{ $errors->has('type_carte_cadeau') ? 'border-red-400 bg-red-500/10' : 'border-amber-400/40' }}" aria-label="Type de carte cadeau">
                            <option value="">— Choisir une carte cadeau —</option>
                            @foreach(\App\Models\Recompense::TYPES_CARTE_CADEAU as $value => $label)
                            <option value="{{ $value }}" {{ old('type_carte_cadeau', '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_carte_cadeau')
                        <p class="mt-2 px-3 py-2 rounded-lg bg-red-500/20 border border-red-400/50 text-red-200 text-sm font-medium flex items-center gap-2" role="alert">
                            <span aria-hidden="true">⚠️</span>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                        <div>
                            <label class="block text-xs text-amber-100/90 mb-1.5">Montant de la carte *</label>
                            <select name="montant_carte_cadeau" required class="cc-montant w-full px-4 py-3 rounded-xl bg-white/10 border border-amber-400/40 text-amber-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" aria-label="Montant">
                                <option value="">— Montant —</option>
                                @foreach($montantsDisponibles as $m)
                                <option value="{{ $m }}" {{ old('montant_carte_cadeau', '') == $m ? 'selected' : '' }}>{{ number_format($m, 0, ',', ' ') }} €</option>
                                @endforeach
                            </select>
                            @error('montant_carte_cadeau')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs text-amber-100/90 mb-1.5">Quantité *</label>
                            <select name="quantite_carte_cadeau" required class="cc-quantite w-full px-4 py-3 rounded-xl bg-white/10 border border-amber-400/40 text-amber-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" aria-label="Quantité">
                                <option value="">— Quantité —</option>
                                @php $oldM = old('montant_carte_cadeau'); $oldQ = old('quantite_carte_cadeau', 1); $maxQte = ($oldM !== '' && $oldM !== null && (float)$oldM > 0) ? (int) floor((float)$rAttente->montant / (float)$oldM) : 0; @endphp
                                @for($q = 1; $q <= $maxQte; $q++)
                                <option value="{{ $q }}" {{ (int)$oldQ === $q ? 'selected' : '' }}>{{ $q }}</option>
                                @endfor
                            </select>
                            @error('quantite_carte_cadeau')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-10 py-4 rounded-2xl bg-neon-green text-white font-bold text-base border-0 shadow-lg shadow-neon-green/40 opacity-70 hover:opacity-100 hover:brightness-110 hover:shadow-xl active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-[#0f172a] cursor-pointer">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    </div>
    <script>
    window.ibanBanqueUrl = {{ Js::from(route('recompenses.iban-banque')) }};
    (function() {
        document.querySelectorAll('.recompense-choix-block').forEach(function(block) {
            var form = block.querySelector('.recompense-choix-form');
            var cards = block.querySelectorAll('.recompense-choix-card');
            var panels = block.querySelectorAll('.recompense-panel');
            var submitBtn = block.querySelector('.recompense-submit-btn');
            var hint = block.querySelector('.recompense-hint');

            function getCheckedType() {
                var r = form.querySelector('input[name="type"]:checked');
                return r ? r.value : '';
            }

            var submitArea = block.querySelector('.recompense-submit-area');
            var panelCarteCadeau = block.querySelector('.recompense-panel-carte_cadeau');

            function selectType(type) {
                form.setAttribute('data-selected-type', type || '');
                var radio = form.querySelector('input[name="type"][value="' + (type || '') + '"]');
                if (radio) radio.checked = true;
                cards.forEach(function(card) {
                    var isSelected = card.getAttribute('data-recompense-type') === type;
                    card.classList.toggle('recompense-card-selected', isSelected);
                    card.classList.toggle('!border-neon-green', isSelected);
                    card.classList.toggle('!bg-neon-green/20', isSelected);
                    card.classList.toggle('!ring-2', isSelected);
                    card.classList.toggle('!ring-neon-green/50', isSelected);
                });
                panels.forEach(function(panel) {
                    var panelType = panel.classList.contains('recompense-panel-virement') ? 'virement' : (panel.classList.contains('recompense-panel-tiktok') ? 'tiktok' : 'carte_cadeau');
                    panel.classList.toggle('hidden', panelType !== type);
                    if (panelType === type && panelType === 'tiktok') panel.classList.add('flex');
                });
                if (panelCarteCadeau) panelCarteCadeau.classList.toggle('hidden', type !== 'carte_cadeau');
                if (submitArea) submitArea.classList.toggle('hidden', type === 'carte_cadeau');
                hint.classList.toggle('hidden', !!type);
            }

            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    selectType(card.getAttribute('data-recompense-type'));
                });
            });

            var initialType = getCheckedType();
            if (initialType) selectType(initialType);

            form.addEventListener('submit', function(e) {
                var t = getCheckedType() || form.getAttribute('data-selected-type') || '';
                var selectedCard = block.querySelector('.recompense-choix-card.recompense-card-selected');
                if (!t && selectedCard) t = selectedCard.getAttribute('data-recompense-type');
                if (t) {
                    var radio = form.querySelector('input[name="type"][value="' + t + '"]');
                    if (radio) radio.checked = true;
                }
                if (t === 'carte_cadeau') {
                    e.preventDefault();
                    return;
                }
                if (!t) {
                    e.preventDefault();
                    hint.classList.remove('hidden');
                    hint.textContent = 'Choisissez d’abord une option (virement, TikTok ou carte cadeau) en cliquant sur une carte.';
                    return;
                }
            });
        });

        // Remplissage automatique du nom de la banque à partir de l’IBAN
        document.querySelectorAll('.formulaire-carte-cadeau').forEach(function(form) {
            var montantTotal = parseFloat(form.getAttribute('data-montant')) || 0;
            var selectMontant = form.querySelector('.cc-montant');
            var selectQuantite = form.querySelector('.cc-quantite');
            function mettreAJourQuantite() {
                var val = parseFloat(selectMontant.value) || 0;
                var selectedQte = selectQuantite.value;
                selectQuantite.innerHTML = '<option value="">— Quantité —</option>';
                if (val > 0 && montantTotal >= val) {
                    var maxQte = Math.floor(montantTotal / val);
                    for (var q = 1; q <= maxQte; q++) {
                        var opt = document.createElement('option');
                        opt.value = q;
                        opt.textContent = q;
                        if (selectedQte && parseInt(selectedQte, 10) === q) opt.selected = true;
                        selectQuantite.appendChild(opt);
                    }
                }
            }
            if (selectMontant && selectQuantite) {
                selectMontant.addEventListener('change', mettreAJourQuantite);
                mettreAJourQuantite();
            }
        });
        var ibanTimeout;
        document.querySelectorAll('.recompense-rib-iban').forEach(function(ibanInput) {
            var form = ibanInput.closest('form');
            var banqueInput = form.querySelector('.recompense-rib-banque');
            var statusEl = form.querySelector('.recompense-iban-status');

            function fetchBanque() {
                var iban = ibanInput.value.replace(/\s/g, '').toUpperCase();
                if (iban.length < 15) {
                    statusEl.textContent = '';
                    return;
                }
                statusEl.textContent = 'Recherche de la banque…';
                statusEl.classList.remove('text-neon-green', 'text-amber-300');
                fetch(window.ibanBanqueUrl + '?iban=' + encodeURIComponent(iban), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.bank) {
                            banqueInput.value = data.bank;
                            statusEl.textContent = 'Banque trouvée : ' + data.bank;
                            statusEl.classList.add('text-neon-green');
                        } else {
                            statusEl.textContent = data.error || 'Banque non trouvée pour cet IBAN. Saisissez le nom manuellement.';
                            statusEl.classList.add('text-amber-300');
                        }
                    })
                    .catch(function() {
                        statusEl.textContent = 'Recherche indisponible. Saisissez le nom de la banque.';
                        statusEl.classList.add('text-amber-300');
                    });
            }

            ibanInput.addEventListener('blur', fetchBanque);
            ibanInput.addEventListener('input', function() {
                clearTimeout(ibanTimeout);
                var iban = ibanInput.value.replace(/\s/g, '');
                if (iban.length >= 15) {
                    ibanTimeout = setTimeout(fetchBanque, 500);
                } else {
                    statusEl.textContent = '';
                }
            });
        });
    })();
    </script>
    @endif

    @if(auth()->user()->canAttribuerOuRecupererRecompense())
    {{-- Formulaire attribuer une récompense — réservé aux fondateurs --}}
    <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-violet-500/5 to-neon-purple/5 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <span class="text-xl">🎁</span> Attribuer une récompense
        </h2>
        <p class="text-sm text-[#94a3b8] mb-4">Le créateur pourra ensuite choisir le mode de réception : virement bancaire, cadeau TikTok ou carte cadeau.</p>
        <form action="{{ route('recompenses.store') }}" method="POST" class="space-y-4" id="form-recompense">
            @csrf
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-0">
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Créateur *</label>
                    <select name="createur_id" required class="w-full min-w-[200px] max-w-[280px] px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-neon-green/50 focus:ring-1 focus:ring-neon-green/30 transition-colors">
                        <option value="">Choisir un créateur</option>
                        @forelse($createurs as $c)
                        <option value="{{ $c->id }}">{{ $c->nom }}{{ $c->pseudo_tiktok ? ' (@' . $c->pseudo_tiktok . ')' : '' }}</option>
                        @empty
                        @endforelse
                    </select>
                    @if($createurs->isEmpty())
                    <p class="mt-1 text-xs text-neon-orange">Aucun créateur dans votre périmètre.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Montant (€) * <span class="text-[#64748b] font-normal">(minimum 0 €)</span></label>
                    <input type="number" name="montant" id="recompense-montant" step="0.01" min="0" required placeholder="0,00" value="{{ old('montant') }}" class="w-24 px-4 py-2.5 rounded-xl bg-white/5 border text-white text-sm focus:outline-none focus:border-neon-green/50 focus:ring-1 focus:ring-neon-green/30 transition-colors {{ $errors->has('montant') ? 'border-red-400' : 'border-white/10' }}">
                    @error('montant')
                    <p class="mt-1 text-sm text-red-400 font-medium" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="min-w-0 flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-[#94a3b8] mb-1.5">Raison (optionnel)</label>
                    <input type="text" name="raison" placeholder="Ex. Top du mois" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b] focus:outline-none focus:border-neon-green/50 focus:ring-1 focus:ring-neon-green/30 transition-colors">
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button type="submit" class="ultra-btn-cta inline-flex items-center gap-2 cursor-pointer border-0"><span><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg> Attribuer</span></button>
            </div>
        </form>
    </div>
    @endif

    {{-- Historique : visible pour tous (montant + liste) ; Facture PDF et Actions réservés au fondateur --}}
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-white/[0.02]">
        <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <span>📋</span> Historique
            </h2>
            @if($recompenses->total() > 0)
            <span class="text-xs text-[#64748b]">{{ $recompenses->total() }} récompense{{ $recompenses->total() > 1 ? 's' : '' }}</span>
            @endif
        </div>

        @if($recompenses->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4 text-4xl">🎁</div>
            <p class="text-[#94a3b8]">Aucune récompense pour l’instant.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 text-left text-[#64748b]">
                        <th class="px-5 py-3 font-semibold">Date</th>
                        @if(auth()->user()->canAttribuerOuRecupererRecompense())
                        <th class="px-5 py-3 font-semibold">Créateur</th>
                        @endif
                        <th class="px-5 py-3 font-semibold">Type</th>
                        <th class="px-5 py-3 font-semibold text-right">Montant</th>
                        <th class="px-5 py-3 font-semibold">Raison</th>
                        <th class="px-5 py-3 font-semibold">Facture</th>
                        @if(auth()->user()->canAttribuerOuRecupererRecompense())
                        <th class="px-5 py-3 font-semibold text-right w-28">Actions</th>
                        @else
                        <th class="px-5 py-3 font-semibold text-right">Détail</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($recompenses as $r)
                    @php
                        $typeLabel = \App\Models\Recompense::TYPES[$r->type] ?? $r->type;
                        $isTiktok = $r->type === 'tiktok' || $r->type === 'TikTok';
                        $enAttenteChoix = $r->isEnAttenteChoix();
                        $supprime = $r->trashed();
                    @endphp
                    <tr class="border-b border-white/5 transition-colors {{ $supprime ? 'opacity-60 bg-red-500/5' : 'hover:bg-white/5' }}">
                        <td class="px-5 py-3.5 text-[#94a3b8]">{{ $r->created_at->format('d/m/Y') }} <span class="text-[#64748b]">{{ $r->created_at->format('H:i') }}</span> @if($supprime)<span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-500/30 text-red-300 border border-red-500/40">Supprimé</span>@endif</td>
                        @if(auth()->user()->canAttribuerOuRecupererRecompense())
                        <td class="px-5 py-3.5 text-white font-medium">{{ $r->createur->nom }}</td>
                        @endif
                        <td class="px-5 py-3.5">
                            @if($enAttenteChoix)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-amber-500/20 text-amber-300 border border-amber-500/30">En attente de choix</span>
                            @elseif($isTiktok)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-black/30 text-white border border-white/10">TikTok</span>
                            @elseif($r->type === 'carte_cadeau' || $r->type === 'Carte cadeau')
                            <span class="inline-flex flex-col gap-0.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-amber-500/20 text-amber-300 border border-amber-500/30">Carte cadeau</span>
                                @if($r->type_carte_cadeau)
                                <span class="text-[10px] text-[#94a3b8]">{{ \App\Models\Recompense::TYPES_CARTE_CADEAU[$r->type_carte_cadeau] ?? $r->type_carte_cadeau }}</span>
                                @endif
                            </span>
                            @elseif($r->type === 'virement' || $r->type === 'PayPal')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">Virement</span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-300 border border-white/10">{{ $typeLabel }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($enAttenteChoix)
                            <span class="font-bold text-neon-green tabular-nums">{{ number_format($r->montant, 2, ',', ' ') }} €</span>
                            @elseif($isTiktok)
                            <span class="font-bold text-neon-green tabular-nums">+ {{ number_format($r->montant_tiktok, 2, ',', ' ') }} €</span>
                            <span class="block text-[10px] text-[#64748b]">(50 % de {{ number_format($r->montant, 2, ',', ' ') }} €)</span>
                            @else
                            <span class="font-bold text-neon-green tabular-nums">+ {{ number_format($r->montant, 2, ',', ' ') }} €</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-[#94a3b8]">{{ $r->raison ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-[#64748b] text-xs">
                            @if($supprime)
                            <span class="text-red-400/80">—</span>
                            @elseif(!$enAttenteChoix)
                            @if($r->factureEstDisponible())
                            <a href="{{ route('recompenses.facture', $r) }}" target="_blank" rel="noopener" class="text-cyan-400 hover:text-cyan-300">Téléchargement disponible pour la facture. Cliquez ici</a>
                            @else
                            <span class="text-amber-400/90" x-data="{ sec: {{ $r->secondesRestantesFacture() }} }" x-init="setInterval(() => { sec = Math.max(0, sec - 1) }, 1000)">
                                <span x-show="sec > 0" x-transition>Disponible dans <span x-text="sec"></span> s</span>
                                <a x-show="sec <= 0" x-transition href="{{ route('recompenses.facture', $r) }}" target="_blank" rel="noopener" class="text-cyan-400 hover:text-cyan-300">Téléchargement disponible pour la facture. Cliquez ici</a>
                            </span>
                            @endif
                            @else
                            <span>—</span>
                            @endif
                        </td>
                        @if(!auth()->user()->canAttribuerOuRecupererRecompense())
                        <td class="px-5 py-3.5 text-right">
                            @if(!$supprime)
                            <a href="{{ route('recompenses.show', $r) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-white/10 text-white/90 hover:bg-white/20 text-sm font-medium transition-colors">Voir</a>
                            @else
                            <span class="text-[#64748b]">—</span>
                            @endif
                        </td>
                        @endif
                        @if(auth()->user()->canAttribuerOuRecupererRecompense())
                        <td class="px-5 py-3.5 text-right">
                            @if($supprime)
                            <span class="text-red-400/90 text-xs font-medium">Supprimé</span>
                            @else
                            <a href="{{ route('recompenses.show', $r) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-neon-green/20 text-neon-green hover:bg-neon-green/30 text-sm font-medium transition-colors mr-2">Voir</a>
                            <form action="{{ route('recompenses.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette récompense ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 border border-red-500/30 hover:border-red-500/50 text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:ring-offset-2 focus:ring-offset-[#0f172a] cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Supprimer
                                </button>
                            </form>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($recompenses->hasPages())
        <div class="px-5 py-4 border-t border-white/10">{{ $recompenses->links() }}</div>
        @endif
        @endif
    </div>

    {{-- Info créditation : petit, en bas, vert — une seule fois --}}
    <p class="text-[10px] md:text-xs text-neon-green/90 text-center pt-2 pb-1" role="note">La somme est créditée entre le 7 et le 12 du mois.</p>
</div>
@endsection
