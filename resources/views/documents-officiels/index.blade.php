@extends('layouts.app')

@section('title', 'Contrat et Règlement')

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
.doc-panel h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 8px;
}
.doc-panel .doc-intro {
    font-size: 0.875rem;
    color: #94a3b8;
    margin-bottom: 24px;
}
.doc-content-block {
    margin-bottom: 28px;
    padding-bottom: 24px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.doc-content-block:last-of-type { border-bottom: 0; margin-bottom: 0; padding-bottom: 0; }
.doc-info-card {
    border-radius: 16px;
    border: 1px solid rgba(14,165,233,0.2);
    background: rgba(14,165,233,0.06);
    padding: 20px 24px;
    margin-bottom: 24px;
}
.doc-info-card-title {
    font-size: 0.6875rem;
    font-weight: 700;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 6px;
}
.doc-info-card-desc { font-size: 0.8125rem; color: #94a3b8; margin-bottom: 16px; }
.doc-form-label { display: block; font-size: 0.8125rem; font-weight: 500; color: #94a3b8; margin-bottom: 6px; }
.doc-form-input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.05);
    color: #fff;
    font-size: 0.9375rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.doc-form-input:focus {
    outline: none;
    border-color: rgba(14,165,233,0.5);
    box-shadow: 0 0 0 3px rgba(14,165,233,0.15);
}
.doc-form-input::placeholder { color: #64748b; }
.doc-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    color: #e2e8f0;
    font-size: 0.875rem;
    font-weight: 600;
    transition: background 0.2s, border-color 0.2s;
}
.doc-btn-secondary:hover { background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.2); color: #fff; }
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
}
.doc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,0.4); color: #fff; }
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
.doc-reglement-summary {
    border-radius: 14px;
    border: 1px solid rgba(14,165,233,0.25);
    background: rgba(14,165,233,0.08);
    padding: 18px 20px;
    margin-bottom: 24px;
}
.doc-reglement-summary h3 { font-size: 0.9375rem; font-weight: 700; color: #fff; margin: 0 0 10px 0; }
.doc-reglement-summary ul { margin: 0; padding-left: 20px; color: #cbd5e1; font-size: 0.8125rem; line-height: 1.6; }
.doc-accept-card {
    border-radius: 16px;
    border: 1px solid rgba(34,197,94,0.3);
    background: rgba(34,197,94,0.08);
    padding: 20px 24px;
    margin-top: 28px;
}
.doc-accept-card .title { font-size: 0.9375rem; font-weight: 700; color: #fff; margin-bottom: 8px; }
.doc-accept-card .desc { font-size: 0.8125rem; color: #94a3b8; margin-bottom: 16px; }
.doc-accept-card label { display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 0.875rem; color: #e2e8f0; }
.doc-accept-card input[type="checkbox"] { margin-top: 3px; accent-color: #22c55e; }
.doc-accept-card .btn-wrap { margin-top: 16px; }
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
    {{-- Hero ── --}}
    <div class="doc-hero">
        <div class="flex items-center gap-4">
            <div class="doc-hero-icon" aria-hidden="true">📄</div>
            <div>
                <h1 class="doc-hero-title">Contrat et Règlement</h1>
                <p class="doc-hero-sub">Page 1 : Contrat de prestation — lisez le contrat puis passez au règlement pour accepter.</p>
            </div>
        </div>
    </div>

    @if(session('warning'))
    <div class="doc-obligation-banner" role="alert">
        ⚠️ {{ session('warning') }}
    </div>
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
    {{-- Statut : contrat + règlement (obligatoire pour accéder au reste) ── --}}
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

    {{-- Section 1 : Contrat (tout sur une seule page) ── --}}
    <section class="doc-panel" id="section-contrat">
        <h2 class="doc-section-title">Contrat officiel de prestation – Unions Agency</h2>
        <p class="doc-intro">Veuillez lire le contrat détaillé ci-dessous et modifier vos informations si besoin. L'acceptation (contrat + règlement) se fait en bas de la page Règlement.</p>
        <div class="doc-content-block">
            @include('documents-officiels.partials.contrat')
        </div>

        @if($createur)
        <div class="doc-info-card">
            <p class="doc-info-card-title">Vos informations</p>
            <p class="doc-info-card-desc">Modifiez si besoin nom, e-mail et téléphone avant de signer. Ces informations figureront sur le contrat.</p>
            <form action="{{ route('documents-officiels.update-info') }}" method="post" class="space-y-4">
                @csrf
                <div>
                    <label for="doc-name" class="doc-form-label">Nom et prénom</label>
                    <input type="text" id="doc-name" name="name" value="{{ old('name', $createur->nom ?? $createur->user?->name ?? '') }}" required maxlength="255" class="doc-form-input">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="doc-email" class="doc-form-label">Adresse e-mail</label>
                    <input type="email" id="doc-email" name="email" value="{{ old('email', $createur->email ?? $createur->user?->email ?? '') }}" required class="doc-form-input">
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="doc-phone" class="doc-form-label">Numéro de téléphone</label>
                    <input type="text" id="doc-phone" name="phone" value="{{ old('phone', $createur->user?->phone ?? '') }}" maxlength="50" placeholder="Optionnel" class="doc-form-input">
                    @error('phone')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <p class="text-[#94a3b8] text-xs mt-2">Nom TikTok (lecture seule) : <strong class="text-white">{{ $createur->pseudo_tiktok ? ltrim($createur->pseudo_tiktok, '@') : ($createur->user?->username ?? '—') }}</strong></p>
                <button type="submit" class="doc-btn-secondary">Enregistrer les modifications</button>
            </form>
        </div>
        @else
        <p class="text-[#94a3b8] text-sm">Les créateurs peuvent lire le contrat et modifier leurs informations. L'acceptation se fait sur la page Règlement.</p>
        @endif
    </section>

    <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
        <a href="{{ route('documents-officiels.reglement') }}" class="doc-btn-primary">
            Suivant : Règlement intérieur
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        <a href="{{ route('aide.index') }}" class="doc-back-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour à Aide & informations
        </a>
    </div>
</div>
@endsection
