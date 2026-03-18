@extends('layouts.app')

@section('title', 'Règlement intérieur – Contrat et Règlement')

@push('styles')
<style>
.doc-page { max-width: 52rem; margin: 0 auto; padding-bottom: 3rem; }
.doc-hero {
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(14,165,233,0.12) 0%, rgba(59,130,246,0.08) 50%, rgba(100,116,139,0.06) 100%);
    border: 1px solid rgba(14,165,233,0.2);
    padding: 24px 28px 28px;
    position: relative;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}
.doc-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #0ea5e9, #3b82f6);
    border-radius: 20px 20px 0 0;
    opacity: 0.9;
}
.doc-hero-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: rgba(14,165,233,0.25);
    border: 1px solid rgba(14,165,233,0.35);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.doc-hero-title { font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
.doc-hero-sub { font-size: 0.875rem; color: #94a3b8; margin-top: 6px; }
.doc-section-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 8px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.doc-panel {
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.03);
    padding: 28px 24px 32px;
    margin-top: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.15);
}
.doc-panel .doc-intro { font-size: 0.875rem; color: #94a3b8; margin-bottom: 24px; }
.doc-reglement-summary {
    border-radius: 14px;
    border: 1px solid rgba(14,165,233,0.25);
    background: rgba(14,165,233,0.08);
    padding: 18px 20px;
    margin-bottom: 24px;
}
.doc-reglement-summary h3 { font-size: 0.9375rem; font-weight: 700; color: #fff; margin: 0 0 10px 0; }
.doc-reglement-summary ul { margin: 0; padding-left: 20px; color: #cbd5e1; font-size: 0.8125rem; line-height: 1.6; }
.doc-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
    border: none;
    color: #fff;
    font-size: 0.9375rem;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(14,165,233,0.35);
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
}
.doc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,0.4); color: #fff; text-decoration: none; }
.doc-signed-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 12px;
    background: rgba(34,197,94,0.15);
    border: 1px solid rgba(34,197,94,0.3);
    color: #86efac;
    font-size: 0.875rem;
    font-weight: 600;
}
.doc-pdf-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    background: rgba(14,165,233,0.2);
    border: 1px solid rgba(14,165,233,0.35);
    color: #7dd3fc;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s, border-color 0.2s;
}
.doc-pdf-link:hover { background: rgba(14,165,233,0.3); border-color: rgba(14,165,233,0.5); color: #fff; text-decoration: none; }
.doc-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #94a3b8;
    text-decoration: none;
    transition: color 0.2s;
}
.doc-back-link:hover { color: #0ea5e9; text-decoration: none; }
.doc-obligation-banner {
    border-radius: 16px;
    border: 1px solid rgba(245,158,11,0.4);
    background: rgba(245,158,11,0.12);
    padding: 16px 20px;
    color: #fcd34d;
    font-size: 0.9375rem;
    font-weight: 500;
}
.doc-status-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}
.doc-status-item {
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.doc-status-item.ok { border-color: rgba(34,197,94,0.3); background: rgba(34,197,94,0.08); }
.doc-status-item .label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
.doc-status-item .value { font-size: 0.875rem; font-weight: 600; color: #fff; }
.doc-status-item.ok .value { color: #86efac; }
.doc-accept-all-card {
    border-radius: 18px;
    border: 1px solid rgba(34,197,94,0.35);
    background: rgba(34,197,94,0.1);
    padding: 24px 28px 28px;
    margin-top: 32px;
}
.doc-accept-all-card .title { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 8px; }
.doc-accept-all-card .desc { font-size: 0.8125rem; color: #94a3b8; margin-bottom: 20px; }
.doc-accept-all-card .checkboxes { display: flex; flex-direction: column; gap: 12px; }
.doc-accept-all-card label { display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 0.875rem; color: #e2e8f0; }
.doc-accept-all-card input[type="checkbox"] { margin-top: 3px; accent-color: #22c55e; }
.doc-accept-all-card .btn-wrap { margin-top: 20px; }
.legal-content { font-size: 0.875rem; color: #cbd5e1; line-height: 1.6; }
.legal-content .text-slate-300 { color: #cbd5e1; }
.legal-content h2 { color: #fff; }
.legal-content strong.text-white { color: #fff; }
.legal-content section { margin-bottom: 1.25rem; }
.legal-content section h2 { font-size: 0.9375rem; margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')
<div class="doc-page space-y-8">
    <div class="doc-hero">
        <div class="flex items-center gap-4">
            <div class="doc-hero-icon" aria-hidden="true">📄</div>
            <div>
                <h1 class="doc-hero-title">Contrat et Règlement</h1>
                <p class="doc-hero-sub">Page 2 : Règlement intérieur — lisez le règlement puis acceptez en bas de page.</p>
            </div>
        </div>
    </div>

    @if(session('warning'))
    <div class="doc-obligation-banner" role="alert">⚠️ {{ session('warning') }}</div>
    @endif
    @if(session('success'))
    <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-sm px-4 py-3 flex items-center gap-2">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div class="rounded-xl bg-sky-500/20 border border-sky-500/40 text-sky-400 text-sm px-4 py-3">{{ session('info') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-xl bg-red-500/20 border border-red-500/40 text-red-400 text-sm px-4 py-3">{{ session('error') }}</div>
    @endif

    @if($createur)
    <div class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
        <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider mb-3">Votre avancement — obligatoire pour accéder à l'application</p>
        <div class="doc-status-grid">
            <div class="doc-status-item {{ $createur->contrat_signe_le ? 'ok' : '' }}">
                <span class="text-lg">{{ $createur->contrat_signe_le ? '✓' : '○' }}</span>
                <div>
                    <span class="label">Contrat</span>
                    <p class="value">{{ $createur->contrat_signe_le ? 'Signé le ' . $createur->contrat_signe_le->format('d/m/Y') : 'À signer' }}</p>
                </div>
            </div>
            <div class="doc-status-item {{ $createur->reglement_accepte_le ? 'ok' : '' }}">
                <span class="text-lg">{{ $createur->reglement_accepte_le ? '✓' : '○' }}</span>
                <div>
                    <span class="label">Règlement intérieur</span>
                    <p class="value">{{ $createur->reglement_accepte_le ? 'Accepté le ' . $createur->reglement_accepte_le->format('d/m/Y') : 'À accepter' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Règlement ── --}}
    <section class="doc-panel" id="section-reglement">
        <h2 class="doc-section-title">Règlement intérieur</h2>
        <p class="doc-intro">Le règlement définit les règles de l'agence : objectifs, lives autorisés et interdits, communication, rôles et sanctions. Acceptez en bas de page pour accéder à l'application.</p>
        <div class="doc-reglement-summary">
            <h3>En résumé</h3>
            <ul>
                <li>Objectif : contenu de qualité, progrès et cadre professionnel</li>
                <li>Lives : contenu engageant uniquement — pas de drama, dodo ou contenu à risque</li>
                <li>Communication : WhatsApp officiel uniquement, pas de sujets sensibles (politique, religion)</li>
                <li>Engagements : 1 match off / mois, réunion mensuelle, respect des consignes agent</li>
                <li>Sanctions : rappel, annulation match, perte de récompenses, exclusion en cas de répétition</li>
            </ul>
        </div>
        <div class="legal-content text-slate-300 text-sm space-y-6">
            @include('documents-officiels.partials.reglement')
        </div>
    </section>

    {{-- En bas : une seule zone pour tout accepter ── --}}
    @if($createur)
    <section class="doc-panel" id="section-acceptation">
        <h2 class="doc-section-title">Acceptation</h2>
        @if($createur->contrat_signe_le && $createur->reglement_accepte_le)
        <div class="doc-accept-all-card">
            <p class="title">Contrat et Règlement acceptés</p>
            <p class="desc">Vous avez signé le contrat et accepté le règlement intérieur. Vous avez accès à l'application.</p>
            <div class="flex flex-wrap items-center gap-4">
                <span class="doc-signed-badge">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Contrat signé le {{ $createur->contrat_signe_le->format('d/m/Y à H:i') }}
                </span>
                <span class="doc-signed-badge">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Règlement accepté le {{ $createur->reglement_accepte_le->format('d/m/Y à H:i') }}
                </span>
                <a href="{{ route('createurs.contrat-pdf', $createur) }}" target="_blank" rel="noopener" class="doc-pdf-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Télécharger le contrat (PDF)
                </a>
            </div>
        </div>
        @else
        <div class="doc-accept-all-card">
            <p class="title">Tout accepter en une fois</p>
            <p class="desc">Après avoir lu le contrat (page 1) et le règlement ci-dessus, cochez les deux cases puis cliquez sur le bouton pour signer le contrat et accepter le règlement intérieur.</p>
            <form action="{{ route('documents-officiels.accept-tout') }}" method="post" onsubmit="return confirm('Vous confirmez signer le contrat et accepter le règlement intérieur. Continuer ?');">
                @csrf
                <div class="checkboxes">
                    <label>
                        <input type="checkbox" name="accept_contrat" value="1" required {{ old('accept_contrat') ? 'checked' : '' }}>
                        <span>J'ai lu et j'accepte le contrat officiel de prestation.</span>
                    </label>
                    <label>
                        <input type="checkbox" name="accept_reglement" value="1" required {{ old('accept_reglement') ? 'checked' : '' }}>
                        <span>J'ai lu et j'accepte le règlement intérieur de l'agence.</span>
                    </label>
                </div>
                @error('accept_contrat')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                @error('accept_reglement')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                <div class="btn-wrap">
                    <button type="submit" class="doc-btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Je signe le contrat et j'accepte le règlement
                    </button>
                </div>
            </form>
        </div>
        @endif
    </section>
    @endif

    <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
        <a href="{{ route('documents-officiels.index') }}" class="doc-back-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            Retour au Contrat
        </a>
        <a href="{{ route('aide.index') }}" class="doc-back-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour à Aide & informations
        </a>
    </div>
</div>
@endsection
