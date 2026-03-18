@extends('layouts.app')

@section('title', 'Récompense #' . $recompense->id)

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-8">
    <a href="{{ route('recompenses.index') }}" class="inline-flex items-center gap-2 text-sm text-[#94a3b8] hover:text-white transition-colors">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour à la liste
    </a>

    @php
        $typeLabel = \App\Models\Recompense::TYPES[$recompense->type] ?? $recompense->type;
        $enAttenteChoix = $recompense->isEnAttenteChoix();
        $estRefusee = $recompense->statut === \App\Models\Recompense::STATUT_REFUSEE;
        $supprime = $recompense->trashed();
        $isVirement = ($recompense->type ?? '') === 'virement';
        $hasRib = $isVirement && ($recompense->rib_iban || $recompense->rib_nom);
        $fondateur = auth()->user()->canAttribuerOuRecupererRecompense();
    @endphp

    {{-- Une seule carte, contenu simple --}}
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <h1 class="text-xl font-bold text-white">Récompense #{{ $recompense->id }}</h1>
            @if($supprime)<span class="px-2 py-0.5 rounded text-xs font-medium bg-red-500/30 text-red-200">Supprimée</span>@endif
            @if($estRefusee)<span class="px-2 py-0.5 rounded text-xs font-medium bg-amber-500/30 text-amber-200">Refusée</span>@endif
            @if($enAttenteChoix && !$estRefusee)<span class="px-2 py-0.5 rounded text-xs font-medium bg-amber-500/20 text-amber-300">En attente de choix</span>@endif
        </div>

        <ul class="space-y-2 text-sm mb-6">
            <li class="flex justify-between gap-4"><span class="text-[#94a3b8]">Date</span><span class="text-white">{{ $recompense->created_at->format('d/m/Y H:i') }}</span></li>
            @if($fondateur)
            <li class="flex justify-between gap-4"><span class="text-[#94a3b8]">Créateur</span><span class="text-white font-medium">{{ $recompense->createur->nom ?? '—' }}</span></li>
            @endif
            <li class="flex justify-between gap-4"><span class="text-[#94a3b8]">Type</span><span class="text-white">{{ $enAttenteChoix ? 'En attente de choix' : $typeLabel }}{{ ($recompense->type ?? '') === 'carte_cadeau' && $recompense->type_carte_cadeau ? ' · ' . (\App\Models\Recompense::TYPES_CARTE_CADEAU[$recompense->type_carte_cadeau] ?? $recompense->type_carte_cadeau) : '' }}</span></li>
            <li class="flex justify-between gap-4"><span class="text-[#94a3b8]">Montant</span><span class="text-neon-green font-bold">{{ number_format($recompense->montant, 2, ',', ' ') }} €</span></li>
            <li class="flex justify-between gap-4"><span class="text-[#94a3b8]">Raison</span><span class="text-white">{{ $recompense->raison ?? '—' }}</span></li>
            @if($recompense->attribuePar)
            <li class="flex justify-between gap-4"><span class="text-[#94a3b8]">Attribuée par</span><span class="text-white/90">{{ $recompense->attribuePar->name }}</span></li>
            @endif
        </ul>

        @if($estRefusee && $recompense->motif_refus)
        <div class="mb-6 p-3 rounded-xl bg-red-500/10 border border-red-500/20">
            <p class="text-xs text-red-300 mb-1">Motif du refus</p>
            <p class="text-red-100 text-sm">{{ $recompense->motif_refus }}</p>
        </div>
        @endif

        @if(($recompense->type ?? '') === 'tiktok' && ($recompense->date_cadeau_tiktok || $recompense->heure_cadeau_tiktok))
        <p class="text-sm text-[#94a3b8] mb-6">Cadeau TikTok : {{ $recompense->date_cadeau_tiktok ? \Carbon\Carbon::parse($recompense->date_cadeau_tiktok)->format('d/m/Y') : '—' }} à {{ $recompense->heure_cadeau_tiktok ?? '—' }}</p>
        @endif

        @if(($recompense->type ?? '') === 'carte_cadeau')
        <div class="mb-6">
            @if($recompense->montant_carte_cadeau && $recompense->quantite_carte_cadeau)
                <p class="text-sm text-[#94a3b8] mb-1">Carte(s) cadeau : {{ $recompense->quantite_carte_cadeau }} × {{ number_format($recompense->montant_carte_cadeau, 0, ',', ' ') }} €</p>
            @endif
            <p class="text-sm text-[#94a3b8] mb-1">Code carte cadeau</p>
            @if($recompense->code_cadeau)
                <div class="mt-2 px-4 py-3 rounded-xl bg-amber-500/25 border-2 border-amber-400/60">
                    <p class="text-amber-50 font-mono text-lg font-bold tracking-wider break-all select-all" style="letter-spacing: 0.15em;">{{ $recompense->code_cadeau }}</p>
                    <p class="text-amber-200/80 text-xs mt-1">Copiez ce code pour l’utiliser sur le site partenaire.</p>
                </div>
            @else
                <p class="text-amber-200/80 text-sm">Non renseigné.</p>
            @endif
            <p class="text-xs text-amber-200/70 mt-2">Valable une seule fois dès utilisation. Expire 1 an à compter de la date d'achat.</p>
            @if($fondateur && !$supprime)
            <form action="{{ route('recompenses.update', $recompense) }}" method="POST" class="mt-2 flex gap-2">
                @csrf
                @method('PUT')
                <input type="text" name="code_cadeau" value="{{ $recompense->code_cadeau }}" placeholder="Saisir le code" class="flex-1 min-w-0 px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white text-sm">
                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-500/30 text-amber-100 text-sm font-medium">Enregistrer</button>
            </form>
            @endif
        </div>
        @endif

        @if($hasRib)
        <div class="mb-6">
            <button type="button" id="toggle-rib" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-500/20 text-blue-200 hover:bg-blue-500/30 text-sm font-medium">
                <span id="rib-btn-text">Afficher le RIB</span>
                <svg class="w-4 h-4" id="rib-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="rib-content" class="hidden mt-3 p-4 rounded-xl bg-white/5 border border-white/10 text-sm space-y-1">
                <p class="text-white font-medium">{{ $recompense->rib_prenom }} {{ $recompense->rib_nom }}</p>
                <p class="font-mono text-cyan-200 break-all">{{ $recompense->rib_iban }}</p>
                <p class="text-[#94a3b8]">{{ $recompense->rib_banque }}</p>
            </div>
            <script>
            (function() {
                var btn = document.getElementById('toggle-rib');
                var content = document.getElementById('rib-content');
                var textEl = document.getElementById('rib-btn-text');
                var chevron = document.getElementById('rib-chevron');
                if (btn && content) {
                    btn.addEventListener('click', function() {
                        content.classList.toggle('hidden');
                        var visible = !content.classList.contains('hidden');
                        if (textEl) textEl.textContent = visible ? 'Masquer le RIB' : 'Afficher le RIB';
                        if (chevron) chevron.style.transform = visible ? 'rotate(180deg)' : '';
                    });
                }
            })();
            </script>
        </div>
        @endif

        <div class="pt-6 border-t border-white/10 flex flex-wrap items-center gap-3">
            @if(!$supprime && !$enAttenteChoix)
            @if($recompense->factureEstDisponible())
            <a href="{{ route('recompenses.facture', $recompense) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-500/20 text-cyan-300 hover:bg-cyan-500/30 text-sm font-medium">
                Téléchargement disponible pour la facture. Cliquez ici
            </a>
            @else
            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500/20 text-amber-200 text-sm font-medium" title="Évite la surcharge du serveur">
                Facture disponible dans {{ $recompense->secondesRestantesFacture() }} s
            </span>
            @endif
            @endif

            @if($fondateur && !$supprime && !$estRefusee)
            <form action="{{ route('recompenses.refuser', $recompense) }}" method="POST" class="flex flex-wrap items-center gap-2 flex-1 min-w-0" onsubmit="return confirm('Refuser cette récompense ? Le motif sera visible par le créateur.');">
                @csrf
                <div class="flex-1 min-w-[160px]">
                    <input type="text" name="motif_refus" required maxlength="2000" placeholder="Motif du refus" class="w-full px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white text-sm placeholder-[#64748b]" value="{{ old('motif_refus') }}">
                    @error('motif_refus')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-500 text-white hover:bg-red-600 text-sm font-medium border border-red-400/50">Refuser</button>
            </form>
            <form action="{{ route('recompenses.destroy', $recompense) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cette récompense ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-500/20 text-red-400 hover:bg-red-500/30 text-sm font-medium">
                    Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
