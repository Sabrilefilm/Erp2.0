<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        
        $annonces = Annonce::query()
            ->when($type !== 'all', fn($q) => $q->byType($type))
            ->orderByOrdre()
            ->get();

        return view('annonces.index', compact('annonces', 'type'));
    }

    public function create()
    {
        $this->authorizeAnnonces();
        return view('annonces.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAnnonces();

        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|in:annonce,evenement,campagne',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
            
            // Validation pour événements
            'date_evenement' => 'required_if:type,evenement|nullable|date',
            'lieu_evenement' => 'required_if:type,evenement|nullable|string|max:255',
            
            // Validation pour campagnes
            'lien_tiktok' => 'nullable|url',
            'hashtag_principal' => 'required_if:type,campagne|nullable|string|max:255',
            'objectif_campagne' => 'required_if:type,campagne|nullable|string',
            'date_debut' => 'required_if:type,campagne|nullable|date',
            'date_fin' => 'required_if:type,campagne|nullable|date|after_or_equal:date_debut',
        ]);

        $contenu = is_string($request->contenu) ? preg_replace('/\r\n|\r/', "\n", $request->contenu) : $request->contenu;

        Annonce::create([
            'titre' => $request->titre,
            'contenu' => $contenu,
            'type' => $request->type,
            'ordre' => (int) ($request->ordre ?? 0),
            'actif' => $request->boolean('actif', true),
            'date_evenement' => $request->date_evenement,
            'lieu_evenement' => $request->lieu_evenement,
            'lien_tiktok' => $request->lien_tiktok,
            'hashtag_principal' => $request->hashtag_principal,
            'objectif_campagne' => $request->objectif_campagne,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
        ]);

        return redirect()->route('annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function edit(Annonce $annonce)
    {
        $this->authorizeAnnonces();
        return view('annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $this->authorizeAnnonces();

        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|in:annonce,evenement,campagne',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
            
            // Validation pour événements
            'date_evenement' => 'required_if:type,evenement|nullable|date',
            'lieu_evenement' => 'required_if:type,evenement|nullable|string|max:255',
            
            // Validation pour campagnes
            'lien_tiktok' => 'nullable|url',
            'hashtag_principal' => 'required_if:type,campagne|nullable|string|max:255',
            'objectif_campagne' => 'required_if:type,campagne|nullable|string',
            'date_debut' => 'required_if:type,campagne|nullable|date',
            'date_fin' => 'required_if:type,campagne|nullable|date|after_or_equal:date_debut',
        ]);

        $contenu = is_string($request->contenu) ? preg_replace('/\r\n|\r/', "\n", $request->contenu) : $request->contenu;

        $annonce->update([
            'titre' => $request->titre,
            'contenu' => $contenu,
            'type' => $request->type,
            'ordre' => (int) ($request->ordre ?? 0),
            'actif' => $request->boolean('actif', true),
            'date_evenement' => $request->date_evenement,
            'lieu_evenement' => $request->lieu_evenement,
            'lien_tiktok' => $request->lien_tiktok,
            'hashtag_principal' => $request->hashtag_principal,
            'objectif_campagne' => $request->objectif_campagne,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
        ]);

        return redirect()->route('annonces.index')->with('success', 'Annonce mise à jour.');
    }

    public function destroy(Annonce $annonce)
    {
        $this->authorizeAnnonces();
        $annonce->delete();
        return back()->with('success', 'Annonce supprimée.');
    }

    private function authorizeAnnonces(): void
    {
        if (! auth()->user()->canAddEntries()) {
            abort(403, 'Seuls les fondateurs, directeurs et manageurs peuvent ajouter ou modifier les annonces.');
        }
    }
}
