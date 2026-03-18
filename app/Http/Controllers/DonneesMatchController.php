<?php

namespace App\Http\Controllers;

use App\Models\CreateurAdverse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Page réservée au fondateur global : gérer le répertoire des créateurs adverses
 * (nom, @ TikTok, téléphone, agent, agence, email…) utilisé pour les matchs.
 */
class DonneesMatchController extends Controller
{
    private function authorizeFondateurGlobal(): void
    {
        if (! auth()->user()?->isFondateurPrincipal()) {
            abort(403, 'Réservé au fondateur global.');
        }
    }

    public function index(Request $request): View
    {
        $this->authorizeFondateurGlobal();

        $query = CreateurAdverse::query();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($qb) use ($q) {
                $qb->where('tiktok_at', 'like', '%' . $q . '%')
                    ->orWhere('nom', 'like', '%' . $q . '%')
                    ->orWhere('agence', 'like', '%' . $q . '%')
                    ->orWhere('agent', 'like', '%' . $q . '%')
                    ->orWhere('telephone', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('autres_infos', 'like', '%' . $q . '%');
            });
        }

        $adverses = $query->orderByRaw('COALESCE(NULLIF(TRIM(agence), ""), "—")')
            ->orderBy('agence')
            ->orderBy('nom')
            ->orderBy('tiktok_at')
            ->get();

        $totalCount = CreateurAdverse::count();

        return view('donnees-match.index', [
            'adverses' => $adverses,
            'totalCount' => $totalCount,
            'searchQuery' => $request->get('q', ''),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeFondateurGlobal();
        $request->validate([
            'tiktok_at' => 'required|string|max:100',
            'nom' => 'nullable|string|max:255',
            'agence' => 'nullable|string|max:255',
            'agent' => 'nullable|string|max:255',
            'telephone' => 'required|string|max:50',
            'email' => 'nullable|string|max:255',
            'autres_infos' => 'nullable|string',
        ]);

        $at = CreateurAdverse::normalizeAt($request->tiktok_at);
        if (empty($at)) {
            return back()->withErrors(['tiktok_at' => 'Le @ TikTok est obligatoire.'])->withInput();
        }
        if (CreateurAdverse::where('tiktok_at', $at)->exists()) {
            return back()->withErrors(['tiktok_at' => 'Ce @ existe déjà dans le répertoire.'])->withInput();
        }

        CreateurAdverse::create([
            'tiktok_at' => $at,
            'nom' => $request->nom ?: null,
            'agence' => $request->agence ?: null,
            'agent' => $request->agent ?: null,
            'telephone' => $request->telephone,
            'email' => $request->email ?: null,
            'autres_infos' => $request->autres_infos ?: null,
        ]);

        return redirect()->route('donnees-match.index')->with('success', 'Contact adverse ajouté.');
    }

    public function edit(int $adverse): View
    {
        $this->authorizeFondateurGlobal();
        $adverse = CreateurAdverse::findOrFail($adverse);
        return view('donnees-match.edit', compact('adverse'));
    }

    public function update(Request $request, int $adverse)
    {
        $this->authorizeFondateurGlobal();
        $request->validate([
            'tiktok_at' => 'required|string|max:100',
            'nom' => 'nullable|string|max:255',
            'agence' => 'nullable|string|max:255',
            'agent' => 'nullable|string|max:255',
            'telephone' => 'required|string|max:50',
            'email' => 'nullable|string|max:255',
            'autres_infos' => 'nullable|string',
        ]);

        $at = CreateurAdverse::normalizeAt($request->tiktok_at);
        if (empty($at)) {
            return back()->withErrors(['tiktok_at' => 'Le @ TikTok est obligatoire.'])->withInput();
        }
        $model = CreateurAdverse::findOrFail($adverse);
        $exists = CreateurAdverse::where('tiktok_at', $at)->where('id', '!=', $model->id)->exists();
        if ($exists) {
            return back()->withErrors(['tiktok_at' => 'Ce @ est déjà utilisé par un autre contact.'])->withInput();
        }

        $model->update([
            'tiktok_at' => $at,
            'nom' => $request->nom ?: null,
            'agence' => $request->agence ?: null,
            'agent' => $request->agent ?: null,
            'telephone' => $request->telephone,
            'email' => $request->email ?: null,
            'autres_infos' => $request->autres_infos ?: null,
        ]);

        return redirect()->route('donnees-match.index')->with('success', 'Contact adverse mis à jour.');
    }

    public function destroy(int $adverse)
    {
        $this->authorizeFondateurGlobal();
        $model = CreateurAdverse::findOrFail($adverse);
        $model->delete();
        return redirect()->route('donnees-match.index')->with('success', 'Contact adverse supprimé.');
    }
}
