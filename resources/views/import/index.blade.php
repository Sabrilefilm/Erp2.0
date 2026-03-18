@extends('layouts.app')

@section('title', 'Import Excel')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Import Excel</h1>
            <p class="text-gray-400 mt-0.5">Seul le Fondateur peut importer des données. L'import est la source de vérité officielle.</p>
        </div>
        <a href="{{ route('import.corriger-heures-jours') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-200 text-sm font-medium shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Corriger heures et jours
        </a>
    </div>

    @php
        $displayLog = session('import_log');
        if (isset($showLog) && $showLog && $showLog->log_detail) {
            $displayLog = $showLog->log_detail;
        }
        $hasResult = ($displayLog && is_array($displayLog)) || session('success') || session('warning') || session('error');
        $countOk = 0;
        $countErr = 0;
        $countTip = 0;
        $errorsAndTips = [];
        if ($displayLog && is_array($displayLog)) {
            foreach ($displayLog as $e) {
                $t = $e['type'] ?? 'info';
                if ($t === 'success') $countOk++;
                elseif ($t === 'error') { $countErr++; $errorsAndTips[] = $e; }
                elseif ($t === 'solution') { $countTip++; $errorsAndTips[] = $e; }
            }
        }
    @endphp

    {{-- Bloc résultat : résumé très visible OK / Erreurs --}}
    <div id="import-result-container" class="space-y-4" style="{{ $hasResult ? '' : 'display:none' }}">
        {{-- Bandeau clair : OK vs Erreurs (rempli aussi en JS après import AJAX) --}}
        <div id="import-result-bandeau">
        @if($displayLog && is_array($displayLog))
        <div class="rounded-xl border border-white/10 overflow-hidden">
            <div class="flex flex-wrap items-center gap-4 px-5 py-4 bg-[#0d1117]">
                <span class="text-white/70 font-medium">Résultat :</span>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 font-semibold">
                    <span aria-hidden="true">✓</span> {{ $countOk }} ligne(s) OK
                </span>
                @if($countErr > 0)
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-500/20 border border-red-500/40 text-red-400 font-semibold">
                    <span aria-hidden="true">✗</span> {{ $countErr }} erreur(s)
                </span>
                @endif
                @if(isset($showLog) && $showLog)
                <span class="text-white/50 text-sm ml-auto">{{ $showLog->created_at->format('d/m/Y H:i') }} · {{ $showLog->fichier }}</span>
                @endif
            </div>
            {{-- Bloc "Erreurs à corriger" en premier si des erreurs --}}
            @if($countErr > 0 && count($errorsAndTips) > 0)
            <div class="px-5 py-4 border-t border-white/10 bg-red-950/20">
                <h3 class="text-sm font-semibold text-red-400 uppercase tracking-wider mb-3">À corriger</h3>
                <ul class="space-y-2 font-mono text-[13px]">
                    @foreach($errorsAndTips as $e)
                    @php $type = $e['type'] ?? ''; $msg = $e['msg'] ?? ''; $line = $e['line'] ?? 0; @endphp
                    <li class="flex gap-3 {{ $type === 'error' ? 'text-red-400' : 'text-[#00d4ff] pl-4 border-l-2 border-[#00d4ff]/50' }}">
                        @if($line > 0)<span class="shrink-0 text-white/40">Ligne {{ $line }}</span>@endif
                        <span class="min-w-0">{{ $msg }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        @endif
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0a0e17] overflow-hidden shadow-2xl shadow-black/40">
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-white/10 bg-[#161b22]">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-[#ff5f57]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#febc2e]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#28c840]"></span>
                    <span class="ml-2 font-mono text-sm font-medium text-white/90">Journal détaillé</span>
                </div>
                <div id="import-terminal-meta" class="font-mono text-xs text-white/50">
                    @if(isset($showLog) && $showLog)
                    {{ $showLog->created_at->format('d/m/Y H:i:s') }} · {{ $showLog->fichier }}
                    @else
                    <span id="import-meta-placeholder">—</span>
                    @endif
                </div>
            </div>
            <div id="import-summary" class="import-summary border-b border-white/10 bg-[#0d1117]/80 px-4 py-3 font-mono text-[13px]">
                @if(session('success'))
                <div class="text-emerald-400">✓ {{ session('success') }}</div>
                @endif
                @if(session('warning'))
                <div class="text-amber-400">⚠ {{ session('warning') }}</div>
                @endif
                @if(session('error'))
                <div class="text-red-400">✗ {{ session('error') }}</div>
                @if(session('import_solution'))
                <div class="mt-2 text-[#7dd3fc] pl-4 border-l-2 border-[#00d4ff]/50">💡 {{ session('import_solution') }}</div>
                @endif
                @endif
                @if(isset($showLog) && $showLog && $showLog->message && !session('success') && !session('warning') && !session('error'))
                <div class="{{ $showLog->statut === 'succes' ? 'text-emerald-400' : ($showLog->statut === 'partiel' ? 'text-amber-400' : 'text-red-400') }}">{{ $showLog->statut === 'succes' ? '✓' : ($showLog->statut === 'partiel' ? '⚠' : '✗') }} {{ $showLog->message }}</div>
                @endif
            </div>
            @php $statsFromLog = isset($showLog) && $showLog ? sprintf('Fichier: %s  |  Lignes importées: %s  |  Erreurs: %s', $showLog->fichier, $showLog->lignes_importees, $showLog->lignes_erreur) : null; @endphp
            <div id="import-stats-block" class="border-b border-white/10 bg-[#161b22]/60 px-4 py-2.5 font-mono text-[12px] text-white/70 {{ $statsFromLog ? '' : 'hidden' }}">
                <span id="import-stats-text">{{ $statsFromLog ?? '—' }}</span>
            </div>
            <div id="import-terminal-wrapper" class="{{ ($displayLog && is_array($displayLog)) ? '' : 'hidden' }}">
                <div class="flex items-center justify-between gap-2 px-4 py-2 border-b border-white/5 bg-black/20">
                    <span class="font-mono text-[11px] text-white/40 uppercase tracking-wider">Sortie détaillée</span>
                    <button type="button" id="import-copy-btn" class="font-mono text-[11px] text-[#00d4ff] hover:text-[#67e8f9] transition-colors" title="Copier tout le journal">Copier</button>
                </div>
                <div id="import-terminal" class="p-4 font-mono text-[13px] leading-[1.6] max-h-[60vh] overflow-y-auto overflow-x-auto whitespace-pre-wrap break-words" style="min-height: 180px;">
                    @if($displayLog && is_array($displayLog))
                    @foreach($displayLog as $entry)
                    @php
                        $type = $entry['type'] ?? 'info';
                        $msg = $entry['msg'] ?? '';
                        $line = $entry['line'] ?? 0;
                        $prefix = $type === 'success' ? '[OK]   ' : ($type === 'error' ? '[ERR]  ' : ($type === 'solution' ? '[TIP]  ' : '[INFO] '));
                    @endphp
                    <div class="flex gap-3 py-0.5 border-b border-white/[0.03] last:border-0 import-log-row
                        {{ $type === 'success' ? 'text-emerald-400' : '' }}
                        {{ $type === 'error' ? 'text-red-400' : '' }}
                        {{ $type === 'solution' ? 'text-[#00d4ff] bg-[#00d4ff]/05 -mx-1 px-2 py-1 rounded' : '' }}
                        {{ $type === 'info' ? 'text-sky-300/90' : '' }}" data-type="{{ $type }}">
                        <span class="shrink-0 text-white/25 select-none w-6 text-right">{{ $line > 0 ? $line : '' }}</span>
                        <span class="shrink-0 text-white/40 select-none w-14 text-right">{{ $prefix }}</span>
                        <span class="min-w-0">{{ $msg }}</span>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 space-y-4">
        <form id="import-form" action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="annee" class="block text-sm font-medium text-gray-300 mb-1">Année des stats</label>
                    <select name="annee" id="annee" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="mois" class="block text-sm font-medium text-gray-300 mb-1">Mois des stats</label>
                    <select name="mois" id="mois" class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white">
                        @foreach(['01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril', '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'] as $num => $nom)
                        <option value="{{ $num }}" {{ $num == now()->format('m') ? 'selected' : '' }}>{{ $nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <p class="text-sm text-gray-500">Choisissez <strong>le mois des données</strong> du fichier. Pour mettre à jour les chiffres visibles sur « Corriger heures et jours » et le dashboard, sélectionnez <strong>le mois en cours</strong>. Les stats (heures, jours, diamants) seront enregistrées pour ce mois.</p>
            <div>
                <label for="fichier" class="block text-sm font-medium text-gray-300 mb-1">Fichier Excel (.xlsx)</label>
                <input type="file" name="fichier" id="fichier" accept=".xlsx,.xls" required class="w-full px-3 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-primary-600 file:text-white">
                @error('fichier')<p class="mt-1 text-sm text-accent-red">{{ $message }}</p>@enderror
            </div>
            <p class="text-sm text-gray-500">
                Colonnes utilisées par l'import : <strong>C</strong> = Nom d'utilisateur, <strong>H</strong> = Diamants, <strong>I</strong> = Durée de LIVE (ex. 7h30), <strong>J</strong> = Jours de passage en LIVE valides.
            </p>
            <div class="flex gap-3 flex-wrap items-center">
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-500 text-white font-medium">Importer</button>
            </div>
        </form>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <h2 class="text-lg font-semibold text-white p-4 border-b border-gray-800">Historique des imports</h2>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px]">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left p-3 text-gray-400 font-medium">Date</th>
                        <th class="text-left p-3 text-gray-400 font-medium">Utilisateur</th>
                        <th class="text-left p-3 text-gray-400 font-medium">Fichier</th>
                        <th class="text-left p-3 text-gray-400 font-medium">Statut</th>
                        <th class="text-right p-3 text-gray-400 font-medium">Lignes</th>
                        <th class="p-3 text-center text-gray-400 font-medium w-20">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-gray-800/50">
                        <td class="p-3 text-sm">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3">{{ $log->user->name }}</td>
                        <td class="p-3 text-sm">{{ $log->fichier }}</td>
                        <td class="p-3">
                            @if($log->statut === 'succes')
                            <span class="text-green-400">Succès</span>
                            @elseif($log->statut === 'partiel')
                            <span class="text-yellow-400">Partiel</span>
                            @else
                            <span class="text-accent-red">Échec</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            {{ $log->lignes_importees }} / {{ $log->lignes_importees + $log->lignes_erreur }}
                            @if($log->log_detail && count($log->log_detail) > 0)
                            <a href="{{ route('import.index', ['show_log' => $log->id]) }}" class="ml-1 text-[11px] text-[#00d4ff] hover:underline">Voir le journal</a>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <form action="{{ route('import.logs.destroy', $log) }}" method="post" class="inline" onsubmit="return confirm('Supprimer cette entrée de l\'historique ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm" title="Supprimer">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">Aucun import.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('import-form');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        var origText = btn ? btn.textContent : '';
        if (btn) { btn.disabled = true; btn.textContent = 'Import en cours…'; }
        var fd = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, status: r.status, data: data }; }).catch(function() { return { ok: false, status: r.status, data: { message: 'Réponse invalide du serveur.' } }; }); })
        .then(function(result) {
            var data = result.data || {};
            var container = document.getElementById('import-result-container');
            var summaryEl = document.getElementById('import-summary');
            var terminalWrap = document.getElementById('import-terminal-wrapper');
            var terminalEl = document.getElementById('import-terminal');
            var metaEl = document.getElementById('import-terminal-meta');
            if (!container || !summaryEl || !terminalEl) return;

            var status = result.ok ? (data.status || 'success') : 'error';
            var msg = data.message || (result.ok ? '' : 'Une erreur s\'est produite.');
            if (data.errors && typeof data.errors === 'object') {
                var parts = [];
                for (var k in data.errors) { if (data.errors[k] && data.errors[k].length) parts.push(data.errors[k].join(' ')); }
                if (parts.length) msg = parts.join(' ');
            }
            var css = status === 'success' ? 'emerald' : (status === 'warning' ? 'amber' : 'red');
            var icon = status === 'success' ? '✓' : (status === 'warning' ? '⚠' : '✗');
            summaryEl.innerHTML = '<div class="text-' + css + '-400">' + icon + ' ' + escapeHtml(msg) + '</div>';
            if (data.solution && (status === 'error' || status === 'warning')) {
                summaryEl.innerHTML += '<div class="mt-2 text-[#7dd3fc] pl-4 border-l-2 border-[#00d4ff]/50">💡 ' + escapeHtml(data.solution) + '</div>';
            }
            if (data.fichier && metaEl) metaEl.textContent = new Date().toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' · ' + data.fichier;
            var statsBlock = document.getElementById('import-stats-block');
            var statsText = document.getElementById('import-stats-text');
            if (statsBlock && statsText && (data.lignes_importees !== undefined || data.fichier)) {
                var parts = [];
                if (data.fichier) parts.push('Fichier: ' + data.fichier);
                if (data.lignes_importees !== undefined) parts.push('Lignes importées: ' + data.lignes_importees);
                if (data.lignes_erreur !== undefined) parts.push('Erreurs: ' + data.lignes_erreur);
                statsText.textContent = parts.join('  |  ');
                statsBlock.classList.remove('hidden');
            }

            if (data.import_log && Array.isArray(data.import_log)) {
                var countOk = 0, countErr = 0, errorsAndTips = [];
                data.import_log.forEach(function(entry) {
                    var t = entry.type || 'info';
                    if (t === 'success') countOk++;
                    else if (t === 'error' || t === 'solution') { if (t === 'error') countErr++; errorsAndTips.push(entry); }
                });
                var bandeauEl = document.getElementById('import-result-bandeau');
                if (bandeauEl) {
                    var bandeauHtml = '<div class="rounded-xl border border-white/10 overflow-hidden"><div class="flex flex-wrap items-center gap-4 px-5 py-4 bg-[#0d1117]">' +
                        '<span class="text-white/70 font-medium">Résultat :</span>' +
                        '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 font-semibold"><span aria-hidden="true">✓</span> ' + countOk + ' ligne(s) OK</span>';
                    if (countErr > 0) bandeauHtml += '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-500/20 border border-red-500/40 text-red-400 font-semibold"><span aria-hidden="true">✗</span> ' + countErr + ' erreur(s)</span>';
                    if (data.fichier) bandeauHtml += '<span class="text-white/50 text-sm ml-auto">' + escapeHtml(data.fichier) + '</span>';
                    bandeauHtml += '</div>';
                    if (countErr > 0 && errorsAndTips.length > 0) {
                        bandeauHtml += '<div class="px-5 py-4 border-t border-white/10 bg-red-950/20"><h3 class="text-sm font-semibold text-red-400 uppercase tracking-wider mb-3">À corriger</h3><ul class="space-y-2 font-mono text-[13px]">';
                        errorsAndTips.forEach(function(e) {
                            var isErr = (e.type || '') === 'error';
                            var lineStr = (e.line > 0) ? 'Ligne ' + e.line : '';
                            bandeauHtml += '<li class="flex gap-3 ' + (isErr ? 'text-red-400' : 'text-[#00d4ff] pl-4 border-l-2 border-[#00d4ff]/50') + '">' + (lineStr ? '<span class="shrink-0 text-white/40">' + escapeHtml(lineStr) + '</span>' : '') + '<span class="min-w-0">' + escapeHtml(e.msg || '') + '</span></li>';
                        });
                        bandeauHtml += '</ul></div>';
                    }
                    bandeauHtml += '</div>';
                    bandeauEl.innerHTML = bandeauHtml;
                }
                var prefix = function(t) { return t === 'success' ? '[OK]   ' : (t === 'error' ? '[ERR]  ' : (t === 'solution' ? '[TIP]  ' : '[INFO] ')); };
                var rowClass = function(t) { return t === 'success' ? 'text-emerald-400' : (t === 'error' ? 'text-red-400' : (t === 'solution' ? 'text-[#00d4ff] bg-[#00d4ff]/05 -mx-1 px-2 py-1 rounded' : 'text-sky-300/90')); };
                terminalEl.innerHTML = data.import_log.map(function(entry) {
                    var type = entry.type || 'info';
                    var line = entry.line || 0;
                    var m = entry.msg || '';
                    var lineStr = line > 0 ? String(line) : '';
                    return '<div class="flex gap-3 py-0.5 border-b border-white/[0.03] last:border-0 ' + rowClass(type) + '"><span class="shrink-0 text-white/25 select-none w-6 text-right">' + escapeHtml(lineStr) + '</span><span class="shrink-0 text-white/40 select-none w-14 text-right">' + escapeHtml(prefix(type)) + '</span><span class="min-w-0">' + escapeHtml(m) + '</span></div>';
                }).join('');
                terminalWrap.classList.remove('hidden');
            } else {
                var bandeauEl = document.getElementById('import-result-bandeau');
                if (bandeauEl) bandeauEl.innerHTML = '';
                terminalWrap.classList.add('hidden');
            }
            container.style.display = '';
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function(err) {
            var container = document.getElementById('import-result-container');
            var summaryEl = document.getElementById('import-summary');
            if (container && summaryEl) {
                summaryEl.innerHTML = '<div class="rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 text-sm">Erreur réseau ou serveur. Réessayez.</div>' +
                    '<div class="rounded-xl bg-[#00d4ff]/10 border border-[#00d4ff]/30 text-[#7dd3fc] px-4 py-3 text-sm flex gap-2 mt-3"><span class="shrink-0">💡</span><div><span class="font-semibold text-[#00d4ff]">Solution :</span><span class="ml-1">Vérifiez votre connexion internet, que le fichier n\'est pas trop volumineux, et réessayez.</span></div></div>';
                container.style.display = '';
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        })
        .finally(function() {
            if (btn) { btn.disabled = false; btn.textContent = origText; }
        });
    });
    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    var copyBtn = document.getElementById('import-copy-btn');
    var terminalEl = document.getElementById('import-terminal');
    if (copyBtn && terminalEl) {
        copyBtn.addEventListener('click', function() {
            var lines = [];
            terminalEl.querySelectorAll('.flex.gap-3').forEach(function(row) {
                var parts = row.querySelectorAll('span');
                if (parts.length >= 3) {
                    var num = (parts[0].textContent || '').trim();
                    var tag = (parts[1].textContent || '').trim();
                    var msg = (parts[2].textContent || '').trim();
                    lines.push((num ? num + '  ' : '') + (tag ? tag + '  ' : '') + msg);
                }
            });
            var text = lines.join('\n');
            if (!text && terminalEl.textContent) text = terminalEl.textContent;
            navigator.clipboard.writeText(text).then(function() {
                var orig = copyBtn.textContent;
                copyBtn.textContent = 'Copié !';
                setTimeout(function() { copyBtn.textContent = orig; }, 2000);
            }).catch(function() { copyBtn.textContent = 'Échec copie'; });
        });
    }
})();
</script>
@endsection
