@extends('layouts.app')

@section('title', 'Messagerie')

@push('styles')
<style>
/* ── Layout messagerie ──────────────────────────── */
.msg-layout {
    display: flex;
    flex-direction: row;
    gap: 0;
    /* Desktop : sous le header (70px) + un peu d'air */
    height: calc(100vh - 130px);
    min-height: 460px;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.02);
}
@media (max-width: 767px) {
    .msg-layout {
        /* Mobile : sous le header (56px) + barre nav bas (75px) + marges */
        height: calc(100vh - 56px - 75px - 24px);
        border-radius: 14px;
    }
    .msg-sidebar { {{ isset($user) ? 'display:none' : '' }} }
    .msg-chat-area { {{ !isset($user) ? 'display:none' : '' }} }
    /* Liste conversations mobile : plus compacte pour laisser la priorité au chat */
    .msg-sidebar-header { padding: 10px 12px; }
    .conv-item { gap: 8px; padding: 8px 12px; border-left-width: 2px; }
    .conv-avatar { width: 32px; height: 32px; border-radius: 8px; font-size: 11px; }
    .conv-name { font-size: 12px; }
    .conv-preview { font-size: 10px; max-width: 140px; }
    .conv-time { font-size: 9px; }
    .conv-badge { min-width: 14px; height: 14px; font-size: 9px; }
    .user-search-result { padding: 6px 12px; gap: 8px; }
}

/* ── Sidebar gauche ─────────────────────────────── */
.msg-sidebar {
    width: 300px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.02);
}
.msg-sidebar-header {
    padding: 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
}
.msg-sidebar-scroll { flex: 1; overflow-y: auto; }
.msg-sidebar-scroll::-webkit-scrollbar { width: 4px; }
.msg-sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

/* Conversation item */
.conv-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    transition: background .15s;
    border-left: 3px solid transparent;
    position: relative;
}
.conv-item:hover { background: rgba(255,255,255,0.05); }
.conv-item.active {
    background: rgba(0,212,255,0.08);
    border-left-color: #00d4ff;
}
/* Conversation avec messages non lus : fond et bordure distincts */
.conv-item.has-unread {
    background: rgba(0,212,255,0.06);
    border-left-color: rgba(0,212,255,0.5);
}
.conv-item.has-unread .conv-name { font-weight: 700; color: rgba(255,255,255,0.98); }
.conv-item.has-unread .conv-preview { color: rgba(0,212,255,0.85); }
.conv-avatar {
    width: 40px; height: 40px; border-radius: 12px;
    background: linear-gradient(135deg, rgba(0,212,255,0.3), rgba(183,148,246,0.3));
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.conv-name { font-size: 13px; font-weight: 600; color: #fff; }
.conv-preview { font-size: 11px; color: rgba(255,255,255,0.35); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
.conv-badge {
    min-width: 18px; height: 18px; border-radius: 9px;
    background: #00d4ff; color: #0a0e27;
    font-size: 10px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    padding: 0 5px; margin-left: auto; flex-shrink: 0;
}
.conv-time { font-size: 10px; color: rgba(255,255,255,0.25); margin-left: auto; flex-shrink: 0; }

/* Filtre rôle pills */
.role-pill {
    display: inline-flex; align-items: center;
    padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500;
    text-decoration: none; border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.4); background: transparent;
    transition: all .15s; cursor: pointer;
}
.role-pill:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.8); }
.role-pill.active { background: rgba(0,212,255,0.15); color: #00d4ff; border-color: rgba(0,212,255,0.3); }

/* Nouveau message : recherche utilisateur */
.user-search-result {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 16px;
    text-decoration: none;
    transition: background .15s;
}
.user-search-result:hover { background: rgba(255,255,255,0.05); }

/* ── Zone chat ──────────────────────────────────── */
.msg-chat-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
}
.msg-chat-header {
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex; align-items: center; gap: 12px;
    flex-shrink: 0;
    background: rgba(255,255,255,0.02);
}
.msg-messages {
    flex: 1; overflow-y: auto;
    padding: 20px 20px 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.msg-messages::-webkit-scrollbar { width: 4px; }
.msg-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }

/* ── Rangée d'un message (avatar + bulle) ────── */
.msg-row {
    display: flex;
    flex-direction: row;   /* avatar et bulle CÔTE À CÔTE */
    align-items: flex-end;
    gap: 8px;
    width: 100%;
}
/* Mes messages : bulle à droite */
.msg-row.is-mine {
    flex-direction: row-reverse;
    justify-content: flex-start;
}
/* Messages reçus : bulle à gauche */
.msg-row.is-theirs {
    flex-direction: row;
    justify-content: flex-start;
}

/* Avatar */
.msg-avatar {
    width: 30px; height: 30px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
    flex-shrink: 0;
    background: linear-gradient(135deg, rgba(183,148,246,0.5), rgba(0,212,255,0.3));
}
.msg-row.is-mine .msg-avatar {
    background: linear-gradient(135deg, rgba(0,212,255,0.4), rgba(14,165,233,0.5));
}

/* Conteneur bulle + heure */
.msg-bubble-col {
    display: flex;
    flex-direction: column;
    max-width: 68%;
    min-width: 0;
}
.msg-row.is-mine .msg-bubble-col { align-items: flex-end; }
.msg-row.is-theirs .msg-bubble-col { align-items: flex-start; }

/* Bulle */
.msg-bubble {
    padding: 9px 14px;
    font-size: 13px; line-height: 1.6;
    word-break: break-word;
    overflow-wrap: break-word;
    border-radius: 16px;
}
/* Messages reçus — 2 tons alternés */
.msg-bubble.theirs-a {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.92);
    border-radius: 4px 16px 16px 16px;
}
.msg-bubble.theirs-b {
    background: rgba(183,148,246,0.13);
    color: rgba(255,255,255,0.92);
    border-radius: 4px 16px 16px 16px;
}
/* Mes messages — cyan */
.msg-bubble.mine {
    background: linear-gradient(135deg, #00d4ff, #0ea5e9);
    color: #0a0e27;
    font-weight: 500;
    border-radius: 16px 4px 16px 16px;
}

/* Heure + accusé */
.msg-time {
    font-size: 10px;
    color: rgba(255,255,255,0.22);
    margin-top: 3px;
    padding: 0 2px;
}

/* Formulaire d'envoi */
.msg-input-bar {
    padding: 12px 16px;
    border-top: 1px solid rgba(255,255,255,0.06);
    display: flex; gap: 10px; align-items: flex-end;
    flex-shrink: 0;
    background: rgba(255,255,255,0.02);
}
.msg-textarea {
    flex: 1; background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px; color: #fff;
    font-size: 13px; line-height: 1.5;
    padding: 10px 14px; resize: none; outline: none;
    transition: border-color .2s;
    max-height: 120px; min-height: 44px;
    font-family: inherit;
}
.msg-textarea::placeholder { color: rgba(255,255,255,0.25); }
.msg-textarea:focus { border-color: rgba(0,212,255,0.4); }
.msg-send-btn {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #00d4ff, #0ea5e9);
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: transform .15s, box-shadow .15s;
    color: #0a0e27;
}
.msg-send-btn:hover { transform: scale(1.05); box-shadow: 0 4px 16px rgba(0,212,255,0.35); }
.msg-file-label { cursor: pointer; display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; transition: border-color .2s, background .2s; }
.msg-file-label:hover { border-color: rgba(0,212,255,0.4); background: rgba(0,212,255,0.08); }
.msg-file-input { position: absolute; width: 0; height: 0; opacity: 0; }
.msg-fichier-link { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; text-decoration: underline; margin-bottom: 4px; }
.msg-bubble.mine .msg-fichier-link { color: #0a0e27; }
.msg-bubble:not(.mine) .msg-fichier-link { color: rgba(255,255,255,0.95); }

/* Placeholder vide */
.msg-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 12px; color: rgba(255,255,255,0.3);
    font-size: 14px; text-align: center; padding: 40px;
}
.msg-empty-icon {
    width: 64px; height: 64px; border-radius: 20px;
    background: rgba(255,255,255,0.05);
    display: flex; align-items: center; justify-content: center;
}

/* Séparateur de date */
.date-sep {
    display: flex; align-items: center; gap: 10px;
    margin: 8px 0; color: rgba(255,255,255,0.2); font-size: 11px;
}
.date-sep::before, .date-sep::after {
    content: ''; flex: 1; height: 1px;
    background: rgba(255,255,255,0.07);
}
</style>
@endpush

@section('content')
<div class="msg-layout">

    {{-- ════════════════════════════════════════════
         SIDEBAR GAUCHE : conversations + recherche
    ════════════════════════════════════════════ --}}
    <aside class="msg-sidebar">
        {{-- Header sidebar --}}
        <div class="msg-sidebar-header">
            <div class="flex items-center justify-between mb-3">
                <h1 class="text-sm font-bold text-white">Messagerie</h1>
                <div class="flex items-center gap-1.5">
                    {{-- Message groupé --}}
                    @if(!auth()->user()->isCreateur())
                    <a href="{{ route('messagerie.groupe') }}" title="Message groupé"
                       class="p-1.5 rounded-lg text-white/30 hover:text-[#00d4ff] hover:bg-[#00d4ff]/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                    @endif
                    <span class="text-[10px] text-white/30 font-semibold uppercase tracking-wider">{{ auth()->user()->getRoleLabel() }}</span>
                </div>
            </div>

            {{-- Info confidentialité --}}
            @if(auth()->user()->isCreateur())
            <p class="text-[10px] text-white/25 leading-relaxed mb-2">Vous pouvez uniquement contacter votre agent assigné.</p>
            @elseif(auth()->user()->isFondateur())
            <p class="text-[10px] text-[#00d4ff]/50 leading-relaxed mb-2">Vue fondateur · Accès à toutes les conversations.</p>
            @endif

            {{-- Filtres par rôle (uniquement pour le fondateur et les rôles avec accès large) --}}
            @if(auth()->user()->isFondateur() || auth()->user()->hasRoleOrAbove('manageur'))
            <div class="flex flex-wrap gap-1.5">
                <a href="{{ route('messagerie.index') }}"
                   class="role-pill {{ !$roleFilter ? 'active' : '' }}">Tous</a>
                @foreach(\App\Models\User::ROLE_LABELS as $val => $label)
                @if($val !== auth()->user()->role)
                <a href="{{ route('messagerie.index', ['role' => $val]) }}"
                   class="role-pill {{ $roleFilter === $val ? 'active' : '' }}">{{ $label }}</a>
                @endif
                @endforeach
            </div>
            @endif
        </div>

        <div class="msg-sidebar-scroll">

            {{-- Conversations existantes --}}
            @if($conversations->isNotEmpty())
            <div class="px-4 pt-3 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/25">Conversations</p>
            </div>
            @foreach($conversations as $otherId => $lastMsg)
            @php
                $other = $interlocutors[$otherId] ?? null;
                if (!$other) continue;
                $isActive = isset($user) && $user->id === $other->id;
                $unread = $unreadCounts[$other->id] ?? 0;
                $initials = strtoupper(mb_substr($other->name, 0, 2));
                $preview = $lastMsg->sender_id === auth()->id() ? 'Vous : ' . $lastMsg->contenu : $lastMsg->contenu;
            @endphp
            <a href="{{ route('messagerie.conversation', $other) }}"
               class="conv-item {{ $isActive ? 'active' : '' }} {{ $unread > 0 ? 'has-unread' : '' }}">
                <div class="conv-avatar">{{ $initials }}</div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="conv-name truncate">{{ $other->name }}</span>
                        <span class="conv-time shrink-0">{{ $lastMsg->created_at->diffForHumans(null, true, true) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <span class="conv-preview">{{ Str::limit($preview, 30) }}</span>
                        @if($unread > 0 && !$isActive)
                        <span class="conv-badge">{{ $unread }}</span>
                        @endif
                    </div>
                    <span class="text-[10px]" style="color: rgba(255,255,255,0.2)">{{ $other->getRoleLabel() }}</span>
                </div>
            </a>
            @endforeach
            @endif

            {{-- Utilisateurs autorisés sans conversation existante --}}
            @php
                $convUserIds = $conversations->keys()->toArray();
                $newUsers = $allowedUsers->filter(fn($u) => !in_array($u->id, $convUserIds));
            @endphp
            @if($newUsers->isNotEmpty())
            <div class="px-4 pt-4 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/25">
                    {{ auth()->user()->isCreateur() ? 'Mon agent' : 'Nouvelle conversation' }}
                </p>
            </div>
            @foreach($newUsers as $u)
            <a href="{{ route('messagerie.conversation', $u) }}"
               class="user-search-result {{ (isset($user) && $user->id === $u->id) ? 'active' : '' }}">
                <div class="conv-avatar" style="width:36px;height:36px;font-size:12px;">{{ strtoupper(mb_substr($u->name, 0, 2)) }}</div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-white/80 truncate">{{ $u->name }}</p>
                    <p class="text-[10px] text-white/30">{{ $u->getRoleLabel() }}</p>
                </div>
            </a>
            @endforeach
            @endif

            @if($allowedUsers->isEmpty())
            <div class="px-4 py-6 text-center">
                <p class="text-xs text-white/30">Aucun contact disponible.</p>
                @if(auth()->user()->isCreateur())
                <p class="text-[10px] text-white/20 mt-1">Aucun agent n'est encore assigné à votre profil.</p>
                @endif
            </div>
            @endif
        </div>
    </aside>

    {{-- ════════════════════════════════════════════
         ZONE CHAT DROITE
    ════════════════════════════════════════════ --}}
    <div class="msg-chat-area">
        @if(isset($user))

        {{-- Header chat --}}
        <div class="msg-chat-header">
            <a href="{{ route('messagerie.index') }}" class="md:hidden p-1.5 rounded-lg text-white/40 hover:text-white hover:bg-white/10 transition-colors mr-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="conv-avatar">{{ strtoupper(mb_substr($user->name, 0, 2)) }}</div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-white">{{ $user->name }}</p>
                <p class="text-[11px] text-white/35">{{ $user->getRoleLabel() }}</p>
            </div>
            {{-- Les messages restent conservés (suppression désactivée) --}}
        </div>

        {{-- Messages --}}
        <div class="msg-messages" id="msg-messages-list">
            @if($messages->isEmpty())
            <div class="flex flex-col items-center justify-center flex-1 gap-3 py-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <p class="text-xs text-white/25">Commencez la conversation avec <strong class="text-white/50">{{ $user->name }}</strong></p>
            </div>
            @else
            @php
                $lastDate    = null;
                $theirIndex  = 0;         // pour alterner les couleurs des messages reçus
                $iAmFondateur = auth()->user()->isFondateur();
            @endphp
            @foreach($messages as $msg)
            @php
                $msgDate         = $msg->created_at->format('Y-m-d');
                $isMine          = $msg->sender_id === auth()->id();
                $initials        = strtoupper(mb_substr($msg->sender->name ?? '?', 0, 2));
                $showReadReceipt = $isMine && $iAmFondateur;

                // Alterner la couleur des messages reçus
                if (!$isMine) { $theirIndex++; }
                $theirClass = ($theirIndex % 2 === 1) ? 'theirs-a' : 'theirs-b';
            @endphp

            {{-- Séparateur de date --}}
            @if($msgDate !== $lastDate)
            <div class="date-sep">{{ $msg->created_at->isSameDay(now()) ? "Aujourd'hui" : $msg->created_at->translatedFormat('d M Y') }}</div>
            @php $lastDate = $msgDate; @endphp
            @endif

            {{-- Rangée message : avatar + bulle côte à côte --}}
            <div class="msg-row {{ $isMine ? 'is-mine' : 'is-theirs' }}">

                {{-- Avatar --}}
                <div class="msg-avatar">{{ $initials }}</div>

                {{-- Bulle + heure --}}
                <div class="msg-bubble-col">
                    <div class="msg-bubble {{ $isMine ? 'mine' : $theirClass }}">
                        @if($msg->fichier_path)
                        <a href="{{ route('messagerie.fichier', $msg) }}" class="msg-fichier-link" target="_blank" rel="noopener">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $msg->fichier_nom ? e($msg->fichier_nom) : 'Télécharger la pièce jointe' }}
                        </a>
                        @if($msg->contenu)<br>@endif
                        @endif
                        @if($msg->contenu){{ $msg->contenu }}@endif
                    </div>
                    <div class="msg-time">
                        {{ $msg->created_at->format('H:i') }}
                        @if($showReadReceipt)
                            @if($msg->read_at)
                            &nbsp;<span style="color:#00d4ff" title="Lu à {{ $msg->read_at->format('H:i') }}">✓✓</span>
                            @else
                            &nbsp;<span style="color:rgba(255,255,255,0.25)" title="Envoyé">✓</span>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
            @endforeach
            @endif
        </div>

        {{-- Formulaire d'envoi (avec pièce jointe optionnelle, max 1 Go) --}}
        <div class="msg-input-bar">
            <form action="{{ route('messagerie.send', $user) }}" method="POST" enctype="multipart/form-data"
                  class="flex flex-col gap-2 w-full"
                  x-data="{ msg: '', fileLabel: '' }"
                  @submit.prevent="if(msg.trim()) $el.submit()">
                @csrf
                <div class="flex gap-2 w-full items-end">
                    <label class="msg-file-label" title="Joindre un fichier (max 1 Go)">
                        <input type="file" name="fichier" class="msg-file-input"
                               accept="*"
                               @change="fileLabel = $el.files[0] ? $el.files[0].name : ''">
                        <svg class="w-5 h-5 text-white/50 hover:text-[#00d4ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    </label>
                    <textarea name="contenu"
                              x-model="msg"
                              class="msg-textarea flex-1"
                              placeholder="Votre message…"
                              rows="1"
                              @keydown.enter.prevent="if(!$event.shiftKey && msg.trim()) $el.form.submit()"
                              @input="$el.style.height='auto'; $el.style.height=Math.min($el.scrollHeight,120)+'px'"
                              maxlength="2000"
                              required></textarea>
                    <button type="submit" class="msg-send-btn" title="Envoyer">
                        <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </div>
                <p class="text-[10px] text-white/35" x-show="fileLabel" x-text="'Fichier : ' + fileLabel"></p>
                <p class="text-[10px] text-white/30">Pièce jointe optionnelle, max 1 Go.</p>
            </form>
        </div>

        @else
        {{-- Aucune conversation sélectionnée --}}
        <div class="msg-empty">
            <div class="msg-empty-icon">
                <svg class="w-7 h-7 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <p class="text-white/30 text-sm">Sélectionnez une conversation<br>ou choisissez un utilisateur à gauche</p>
        </div>
        @endif
    </div>
</div>

<script>
// Auto-scroll vers le dernier message
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('msg-messages-list');
    if (list) list.scrollTop = list.scrollHeight;
});
</script>
@endsection
