@extends('layouts.app')

@section('title', 'Message groupé')

@section('content')
<div class="max-w-2xl mx-auto pb-10">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('messagerie.index') }}" class="p-2 -ml-2 rounded-xl text-[#94a3b8] hover:text-white hover:bg-white/10 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-white">Message groupé</h1>
            <p class="text-sm text-[#94a3b8] mt-0.5">
                @if(auth()->user()->isAgent() || auth()->user()->isAmbassadeur())
                    Envoyez le même message à vos créateurs
                @else
                    Envoyez le même message à plusieurs personnes
                @endif
            </p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-[#00d4ff]/10 border border-[#00d4ff]/30 text-[#00d4ff] text-sm px-4 py-3 mb-5">{{ session('success') }}</div>
    @endif

    @if($allowedUsers->isEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-10 text-center">
        <p class="text-white/30 text-sm">Aucun contact disponible pour un message groupé.</p>
    </div>
    @else
    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
        <form action="{{ route('messagerie.groupe.send') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Sélection des destinataires --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-white">Destinataires</label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="toggleAll(true)"
                                class="text-[11px] text-[#00d4ff] hover:underline">Tout sélectionner</button>
                        <span class="text-white/20">·</span>
                        <button type="button" onclick="toggleAll(false)"
                                class="text-[11px] text-[#94a3b8] hover:underline">Tout désélectionner</button>
                    </div>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1" id="destinataires-list">
                    @foreach($allowedUsers as $u)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-white/08 bg-white/[0.02] cursor-pointer hover:bg-white/[0.05] transition-colors has-[:checked]:border-[#00d4ff]/30 has-[:checked]:bg-[#00d4ff]/05">
                        <input type="checkbox" name="destinataires[]" value="{{ $u->id }}"
                               class="dest-check w-4 h-4 rounded accent-[#00d4ff] cursor-pointer">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold shrink-0"
                             style="background:linear-gradient(135deg,rgba(0,212,255,0.25),rgba(183,148,246,0.25));color:#fff">
                            {{ strtoupper(mb_substr($u->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-white truncate">{{ $u->name }}</p>
                            <p class="text-[10px] text-white/35">{{ $u->getRoleLabel() }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('destinataires')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Message</label>
                <textarea name="contenu" rows="5" required maxlength="2000"
                          placeholder="Rédigez votre message…"
                          class="w-full bg-white/05 border border-white/10 rounded-xl text-white text-sm px-4 py-3 resize-y focus:outline-none focus:border-[#00d4ff]/50 placeholder-white/25">{{ old('contenu') }}</textarea>
                @error('contenu')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pièce jointe optionnelle (max 1 Go) --}}
            <div>
                <label class="block text-sm font-semibold text-white mb-2">Fichier joint (optionnel)</label>
                <input type="file" name="fichier" class="w-full text-sm text-white/80 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-[#00d4ff]/20 file:text-[#00d4ff] hover:file:bg-[#00d4ff]/30 file:cursor-pointer">
                <p class="text-[11px] text-white/35 mt-1">Un seul fichier par envoi, taille max 1 Go.</p>
                @error('fichier')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-[#00d4ff] hover:bg-[#00d4ff]/90 text-[#0a0e27] font-bold text-sm transition-all shadow-lg shadow-[#00d4ff]/20">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    Envoyer à tous les sélectionnés
                </button>
                <a href="{{ route('messagerie.index') }}"
                   class="px-4 py-3 rounded-xl border border-white/10 text-[#94a3b8] hover:text-white text-sm font-medium transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
    @endif
</div>

<script>
function toggleAll(state) {
    document.querySelectorAll('.dest-check').forEach(cb => cb.checked = state);
}
</script>
@endsection
