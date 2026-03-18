<?php

namespace App\Http\Controllers;

use App\Models\Regle;
use Illuminate\Http\Request;

class RegleController extends Controller
{
    public function index(Request $request)
    {
        $regles = Regle::orderBy('ordre')->orderBy('id')->get();

        return view('regles.index', compact('regles'));
    }

    public function create()
    {
        $this->authorizeRegles();
        return view('regles.create');
    }

    public function store(Request $request)
    {
        $this->authorizeRegles();

        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $contenu = is_string($request->contenu) ? preg_replace('/\r\n|\r/', "\n", $request->contenu) : $request->contenu;

        Regle::create([
            'titre' => $request->titre,
            'contenu' => $contenu,
            'ordre' => (int) ($request->ordre ?? 0),
            'actif' => $request->boolean('actif'),
        ]);

        return redirect()->route('regles.index')->with('success', 'Message ajouté.');
    }

    public function edit(Regle $regle)
    {
        $this->authorizeRegles();
        return view('regles.edit', compact('regle'));
    }

    public function update(Request $request, Regle $regle)
    {
        $this->authorizeRegles();

        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $contenu = is_string($request->contenu) ? preg_replace('/\r\n|\r/', "\n", $request->contenu) : $request->contenu;

        $regle->update([
            'titre' => $request->titre,
            'contenu' => $contenu,
            'ordre' => (int) ($request->ordre ?? 0),
            'actif' => $request->boolean('actif'),
        ]);

        return redirect()->route('regles.index')->with('success', 'Message mis à jour.');
    }

    public function destroy(Regle $regle)
    {
        $this->authorizeRegles();
        $regle->delete();
        return back()->with('success', 'Règle supprimée.');
    }

    private function authorizeRegles(): void
    {
        if (! auth()->user()->canAddEntries()) {
            abort(403, 'Seuls les fondateurs, directeurs et manageurs peuvent ajouter ou modifier les règles.');
        }
    }
}
