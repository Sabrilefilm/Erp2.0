<table class="w-full min-w-[500px]">
    <thead>
        <tr class="border-b border-white/10 bg-white/5">
            <th class="text-left px-5 py-3 font-semibold text-white">Nom</th>
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Utilisateur</th>
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Rôle</th>
            @if(auth()->user()->isFondateurPrincipal())
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Agence / Sous-agence</th>
            @endif
            <th class="text-left px-5 py-3 font-semibold text-[#94a3b8]">Agent / Manageur</th>
            @if(auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur() || auth()->user()->isManageur() || auth()->user()->isSousManager())
            <th class="text-center px-5 py-3 font-semibold text-[#94a3b8] w-24">Contrat</th>
            <th class="px-5 py-3 font-semibold text-white text-right">Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($users as $u)
        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
            <td class="px-5 py-3.5 font-medium text-white">{{ $u->name }}</td>
            <td class="px-5 py-3.5 text-[#94a3b8]">{{ $u->username ?? '—' }}</td>
            <td class="px-5 py-3.5 text-[#94a3b8]">{{ $u->getRoleLabel() }}</td>
            @if(auth()->user()->isFondateurPrincipal())
            <td class="px-5 py-3.5 text-[#94a3b8]">{{ $u->equipe?->nom ?? ($u->createur?->equipe?->nom ?? '—') }}</td>
            @endif
            <td class="px-5 py-3.5 text-[#94a3b8]">
                @if($u->isCreateur())
                    @php
                        $agentName = $u->createur?->agent?->name ?? $u->equipe?->agents->first()?->name ?? null;
                        $manageurName = $u->createur?->equipe?->manager?->name ?? $u->equipe?->manager?->name ?? null;
                    @endphp
                    @if($agentName || $manageurName)
                        @if($agentName)<span class="block"><span class="text-white/50 text-xs">Agent :</span> {{ $agentName }}</span>@endif
                        @if($manageurName)<span class="block text-white/60 text-xs"><span class="text-white/40">Mg :</span> {{ $manageurName }}</span>@endif
                    @else
                        <span class="text-white/40">—</span>
                    @endif
                @else
                    <span class="text-white/40">—</span>
                @endif
            </td>
            @if(auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur() || auth()->user()->isManageur() || auth()->user()->isSousManager())
            <td class="px-5 py-3.5 text-center">
                @if($u->isCreateur() && $u->createur)
                    @if($u->createur->contrat_signe_le)
                    <a href="{{ route('createurs.contrat-pdf', $u->createur) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 font-medium" title="Télécharger le contrat signé">✓ Signé</a>
                    @else
                    <span class="text-red-400" title="Non signé">❌</span>
                    @endif
                @else
                <span class="text-white/30">—</span>
                @endif
            </td>
            <td class="px-5 py-3.5 text-right">
                @can('update', $u)
                <a href="{{ route('users.edit', $u) }}" class="text-sky-400 hover:text-sky-300 font-medium">Modifier</a>
                @endcan
                @can('delete', $u)
                <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-300 font-medium">Supprimer</button>
                </form>
                @endcan
            </td>
            @endif
        </tr>
        @empty
        <tr>
            @php
                $nbCols = 4; // Nom, Utilisateur, Rôle, Agent (ou Agence si fondateur)
                if (auth()->user()->isFondateurPrincipal()) { $nbCols++; } // Agence
                if (auth()->user()->isFondateur() || auth()->user()->isDirecteur() || auth()->user()->isSousDirecteur() || auth()->user()->isManageur() || auth()->user()->isSousManager()) { $nbCols += 2; } // Contrat + Action
            @endphp
            <td colspan="{{ $nbCols }}" class="px-5 py-12 text-center text-[#94a3b8]">Aucun utilisateur.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@if($users->hasPages())
<div class="px-5 py-4 border-t border-white/10">
    {{ $users->withPath(route('users.index'))->appends(request()->only('role', 'q'))->links() }}
</div>
@endif
