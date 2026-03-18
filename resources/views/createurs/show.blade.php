@extends('layouts.app')

@section('title', $createur->nom)

@section('content')
<div class="space-y-6">
    {{-- En-tête : nom, retour, lien TikTok --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4 flex-wrap">
            <a href="{{ route('createurs.index') }}" class="text-[#b0bee3] hover:text-white transition-colors inline-flex items-center gap-1 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-neon-blue to-neon-purple bg-clip-text text-transparent">{{ $createur->nom }}</h1>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($createur->pseudo_tiktok)
            @php $pseudo = ltrim($createur->pseudo_tiktok, '@'); @endphp
            <a href="https://www.tiktok.com/{{ '@' . $pseudo }}" target="_blank" rel="noopener noreferrer" class="ultra-btn-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88 2.2V9.4a6.84 6.84 0 0 0-1.05-.08A5.33 5.33 0 0 0 5 20.1a5.34 5.34 0 0 0 10.86 0v-7.27a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                Voir le profil TikTok
            </a>
            @endif
            @can('view', $createur)
            <a href="{{ route('createurs.contrat-pdf', $createur) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold text-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Contrat de prestation (PDF)
            </a>
            @endcan
        </div>
    </div>

    {{-- Choix du mois (voir les mois en arrière) --}}
    @if($moisDisponibles && $moisDisponibles->isNotEmpty())
    <div class="flex flex-wrap items-center gap-3 mb-2">
        <span class="text-[#6b7a9f] text-sm">Stats stream :</span>
        <form id="form-mois-createur" method="get" action="{{ route('createurs.show', $createur) }}" class="flex items-center gap-2">
            <select name="annee" class="rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2 focus:ring-2 focus:ring-neon-blue">
                @foreach($moisDisponibles->pluck('annee')->unique()->sortDesc()->values() as $y)
                <option value="{{ $y }}" {{ (int)$annee === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="mois" class="rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2 focus:ring-2 focus:ring-neon-blue">
                @foreach($moisDisponibles->where('annee', $annee) as $m)
                <option value="{{ $m['mois'] }}" {{ (int)$mois === (int)$m['mois'] ? 'selected' : '' }}>{{ $m['libelle'] }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <script>
    (function(){
        var f = document.getElementById('form-mois-createur');
        if (!f) return;
        var base = f.getAttribute('action') || '';
        function go(){ var a = f.querySelector('[name=annee]').value; var m = f.querySelector('[name=mois]').value; window.location.href = base + (base.indexOf('?') >= 0 ? '&' : '?') + 'annee=' + encodeURIComponent(a) + '&mois=' + encodeURIComponent(m); }
        f.querySelectorAll('select').forEach(function(s){ s.addEventListener('change', go); });
    })();
    </script>
    @endif

    {{-- Stats : vues, followers, engagement — puis une ligne pour jours / heures / diamants --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="ultra-card rounded-xl p-4 border border-white/10">
            <p class="text-[#6b7a9f] text-sm">Vues</p>
            <p class="text-xl font-bold text-white mt-1">{{ number_format($createur->stats_vues ?? 0, 0, ',', ' ') }}</p>
        </div>
        <div class="ultra-card rounded-xl p-4 border border-white/10">
            <p class="text-[#6b7a9f] text-sm">Followers</p>
            <p class="text-xl font-bold text-white mt-1">{{ number_format($createur->stats_followers ?? 0, 0, ',', ' ') }}</p>
        </div>
        <div class="ultra-card rounded-xl p-4 border border-white/10">
            <p class="text-[#6b7a9f] text-sm">Engagement</p>
            <p class="text-xl font-bold text-white mt-1">{{ $createur->stats_engagement ? number_format($createur->stats_engagement, 2).'%' : '—' }}</p>
        </div>
    </div>
    {{-- Jours / Heures / Diamants — données issues de l'import officiel (Excel Fondateur), pas calculées --}}
    <div class="grid grid-cols-3 gap-3 md:gap-4">
        <div class="ultra-card rounded-xl p-4 border border-white/10">
            <p class="text-[#6b7a9f] text-sm">📅 Jours stream</p>
            <p class="text-xl font-bold text-white mt-1">{{ isset($jours) && $jours !== null ? $jours . ' jours' : '—' }}</p>
        </div>
        <div class="ultra-card rounded-xl p-4 border border-white/10">
            <p class="text-[#6b7a9f] text-sm">⏱️ Heures stream</p>
            <p class="text-xl font-bold text-white mt-1">{{ isset($heures) && $heures !== null ? \App\Support\HeuresHelper::format((float) $heures) : '—' }}</p>
        </div>
        <div class="ultra-card rounded-xl p-4 border border-white/10">
            <p class="text-[#6b7a9f] text-sm">💎 Diamants</p>
            <p class="text-xl font-bold text-white mt-1">{{ isset($diamants) && $diamants !== null ? number_format($diamants, 0, ',', ' ') : '—' }}</p>
        </div>
    </div>
    <p class="text-[#94a3b8] text-xs -mt-2">Données basées sur l'import Excel (Fondateur), pas calculées ni inventées. Objectif : 7 jours, 16 heures de stream.</p>
    <div class="ultra-card rounded-xl p-4 border border-white/10">
        <p class="text-[#6b7a9f] text-sm">Statut</p>
        <p class="text-lg font-semibold text-[#b0bee3] mt-1">{{ $createur->statut ?? '—' }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Attribution agent / équipe / ambassadeur (staff) --}}
        @if(auth()->user()->can('update', $createur) && isset($agents) && $agents->isNotEmpty())
        <div class="ultra-card rounded-xl p-6 border border-white/10 space-y-4">
            <h2 class="text-lg font-semibold text-white border-b border-white/10 pb-2">Attribution</h2>
            <form action="{{ route('createurs.update-attribution', $createur) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="attribution_agent_id" class="block text-sm font-medium text-[#b0bee3] mb-1">Agent</label>
                    <select name="agent_id" id="attribution_agent_id" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                        <option value="">—</option>
                        @foreach($agents as $a)
                        <option value="{{ $a->id }}" {{ old('agent_id', $createur->agent_id) == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="attribution_equipe_id" class="block text-sm font-medium text-[#b0bee3] mb-1">Équipe</label>
                    <select name="equipe_id" id="attribution_equipe_id" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                        <option value="">—</option>
                        @foreach($equipes ?? [] as $eq)
                        <option value="{{ $eq->id }}" {{ old('equipe_id', $createur->equipe_id) == $eq->id ? 'selected' : '' }}>{{ $eq->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="attribution_ambassadeur_id" class="block text-sm font-medium text-[#b0bee3] mb-1">Ambassadeur</label>
                    <select name="ambassadeur_id" id="attribution_ambassadeur_id" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                        <option value="">—</option>
                        @foreach($ambassadeurs ?? [] as $amb)
                        <option value="{{ $amb->id }}" {{ old('ambassadeur_id', $createur->ambassadeur_id) == $amb->id ? 'selected' : '' }}>{{ $amb->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="ultra-btn-primary px-4 py-2.5 rounded-xl font-semibold text-sm"><span>Enregistrer l'attribution</span></button>
            </form>
            @if(session('success'))
            <p class="text-sm text-emerald-400">{{ session('success') }}</p>
            @endif
        </div>
        @endif

        {{-- Infos + missions --}}
        <div class="ultra-card rounded-xl p-6 border border-white/10 space-y-4">
            <h2 class="text-lg font-semibold text-white border-b border-white/10 pb-2">Informations</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                <dt class="text-[#6b7a9f]">Email</dt><dd class="text-[#b0bee3]">{{ $createur->email ?? '—' }}</dd>
                <dt class="text-[#6b7a9f]">Pseudo TikTok</dt><dd class="text-[#b0bee3]">{{ $createur->pseudo_tiktok ? '@' . ltrim($createur->pseudo_tiktok, '@') : '—' }}</dd>
                <dt class="text-[#6b7a9f]">Équipe</dt><dd class="text-[#b0bee3]">{{ $createur->equipe?->nom ?? '—' }}</dd>
                <dt class="text-[#6b7a9f]">Agent</dt><dd class="text-[#b0bee3]">{{ $createur->agent?->name ?? '—' }}</dd>
                <dt class="text-[#6b7a9f]">Ambassadeur</dt><dd class="text-[#b0bee3]">{{ $createur->ambassadeur?->name ?? '—' }}</dd>
            </dl>
            @if($createur->missions)
            <div>
                <p class="text-[#6b7a9f] text-sm">Missions</p>
                <p class="text-[#b0bee3] mt-1">{{ $createur->missions }}</p>
            </div>
            @endif
        </div>

        {{-- Notes + commentaires --}}
        <div class="ultra-card rounded-xl p-6 border border-white/10 space-y-4">
            @if(auth()->user()->can('update', $createur))
            <form action="{{ route('createurs.update-notes', $createur) }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <label class="block text-sm text-[#b0bee3]">Statut (note interne)</label>
                <input type="text" name="statut" value="{{ old('statut', $createur->statut) }}" class="ultra-input w-full px-3 py-2 rounded-xl text-white">
                <label class="block text-sm text-[#b0bee3]">Notes</label>
                <textarea name="notes" rows="3" class="ultra-input w-full px-3 py-2 rounded-xl text-white">{{ old('notes', $createur->notes) }}</textarea>
                <button type="submit" class="ultra-btn-primary px-4 py-2.5 rounded-xl font-semibold text-sm"><span>Enregistrer</span></button>
            </form>
            @else
            <div>
                <p class="text-[#6b7a9f] text-sm">Notes</p>
                <p class="text-[#b0bee3] mt-1">{{ $createur->notes ?? '—' }}</p>
            </div>
            @endif

            <h2 class="text-lg font-semibold text-white border-b border-white/10 pb-2 pt-4">Commentaires internes</h2>
            @if(auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousManager() || auth()->user()->isAgent())
            <form action="{{ route('createurs.commentaires.store', $createur) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="contenu" placeholder="Nouveau commentaire…" required class="ultra-input flex-1 px-3 py-2 rounded-xl text-white placeholder-[#6b7a9f]">
                <button type="submit" class="ultra-btn-primary px-4 py-2 rounded-xl font-semibold text-sm"><span>Ajouter</span></button>
            </form>
            @endif
            <ul class="space-y-2 max-h-48 overflow-y-auto">
                @forelse($commentaires as $com)
                <li class="text-sm p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="text-[#6b7a9f]">{{ $com->user->name }} · {{ $com->created_at->format('d/m/Y H:i') }}</span>
                    <p class="text-[#b0bee3] mt-1">{{ $com->contenu }}</p>
                </li>
                @empty
                <li class="text-[#6b7a9f] text-sm">Aucun commentaire.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
