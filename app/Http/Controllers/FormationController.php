<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationCatalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormationController extends Controller
{
    /** Catalogue des formations — page unique (style V1). */
    public function index(Request $request)
    {
        $query = Formation::orderBy('ordre')->orderBy('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('titre', 'like', '%'.$q.'%')
                    ->orWhere('description', 'like', '%'.$q.'%');
            });
        }
        if ($request->filled('type') && in_array($request->type, array_keys(Formation::TYPES))) {
            $query->where('type', $request->type);
        }
        if ($request->filled('catalogue') && FormationCatalogue::where('slug', $request->catalogue)->exists()) {
            $query->where('catalogue', $request->catalogue);
        }

        $formations = $query->withCount('questions')->get();
        $catalogues = FormationCatalogue::orderBy('ordre')->get();

        return view('formations.index', compact('formations', 'catalogues'));
    }

    public function show(Formation $formation)
    {
        $all = Formation::orderBy('ordre')->orderBy('id')->get();
        $idx = $all->search(fn ($f) => $f->id === $formation->id);
        $prevFormation = $idx > 0 ? $all[$idx - 1] : null;
        $nextFormation = $idx !== false && $idx < $all->count() - 1 ? $all[$idx + 1] : null;

        return view('formations.show', compact('formation', 'prevFormation', 'nextFormation'));
    }

    public function contenu(Formation $formation)
    {
        $all = Formation::orderBy('ordre')->orderBy('id')->get();
        $idx = $all->search(fn ($f) => $f->id === $formation->id);
        $prevFormation = $idx > 0 ? $all[$idx - 1] : null;
        $nextFormation = $idx !== false && $idx < $all->count() - 1 ? $all[$idx + 1] : null;

        return view('formations.contenu', compact('formation', 'prevFormation', 'nextFormation'));
    }

    public function create()
    {
        $this->authorizeFormations();
        $catalogues = FormationCatalogue::orderBy('ordre')->get();
        return view('formations.create', compact('catalogues'));
    }

    public function store(Request $request)
    {
        $this->authorizeFormations();

        $maxFileKb = 10 * 1024 * 1024; // 10 Go en kilo-octets
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mots_cles' => 'nullable|string|max:1000',
            'type' => 'required|in:'.implode(',', array_keys(Formation::TYPES)),
            'url' => 'nullable|string|max:500',
            'media' => 'nullable|file|max:'.$maxFileKb,
            'fichier' => 'nullable|file|max:'.$maxFileKb,
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
            'catalogue' => 'nullable|string|max:64',
        ], [
            'fichier.uploaded' => 'Le fichier n\'a pas pu être envoyé. Vérifiez que php.ini autorise les gros fichiers : upload_max_filesize et post_max_size à 10G ou plus.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 10 Go.',
            'media.uploaded' => 'Le média n\'a pas pu être envoyé. Vérifiez que php.ini autorise les gros fichiers : upload_max_filesize et post_max_size à 10G ou plus.',
            'media.max' => 'Le média ne doit pas dépasser 10 Go.',
        ]);
        if ($request->filled('catalogue') && ! FormationCatalogue::where('slug', $request->catalogue)->exists()) {
            return back()->withErrors(['catalogue' => 'Ce catalogue n\'existe pas.'])->withInput();
        }

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('formations/media', 'local');
        }
        $fichierPath = null;
        $fichierNom = null;
        if ($request->hasFile('fichier')) {
            $f = $request->file('fichier');
            $fichierPath = $f->store('formations/fichiers', 'local');
            $fichierNom = $f->getClientOriginalName();
        }

        Formation::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'mots_cles' => $request->mots_cles ?: null,
            'type' => $request->type,
            'catalogue' => $request->catalogue ?: null,
            'url' => $request->url ?: null,
            'media_path' => $mediaPath,
            'fichier_path' => $fichierPath,
            'fichier_nom' => $fichierNom,
            'ordre' => (int) ($request->ordre ?? 0),
            'actif' => $request->boolean('actif'),
        ]);

        return redirect()->route('formations.index')->with('success', 'Formation / contenu ajouté.');
    }

    public function edit(Formation $formation)
    {
        $this->authorizeFormations();
        $catalogues = FormationCatalogue::orderBy('ordre')->get();
        return view('formations.edit', compact('formation', 'catalogues'));
    }

    public function update(Request $request, Formation $formation)
    {
        $this->authorizeFormations();

        $maxFileKb = 10 * 1024 * 1024; // 10 Go
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mots_cles' => 'nullable|string|max:1000',
            'type' => 'required|in:'.implode(',', array_keys(Formation::TYPES)),
            'url' => 'nullable|string|max:500',
            'media' => 'nullable|file|max:'.$maxFileKb,
            'fichier' => 'nullable|file|max:'.$maxFileKb,
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
            'catalogue' => 'nullable|string|max:64',
        ], [
            'fichier.uploaded' => 'Le fichier n\'a pas pu être envoyé. Vérifiez que php.ini autorise les gros fichiers : upload_max_filesize et post_max_size à 10G ou plus.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 10 Go.',
            'media.uploaded' => 'Le média n\'a pas pu être envoyé. Vérifiez que php.ini autorise les gros fichiers : upload_max_filesize et post_max_size à 10G ou plus.',
            'media.max' => 'Le média ne doit pas dépasser 10 Go.',
        ]);
        if ($request->filled('catalogue') && ! FormationCatalogue::where('slug', $request->catalogue)->exists()) {
            return back()->withErrors(['catalogue' => 'Ce catalogue n\'existe pas.'])->withInput();
        }

        $data = [
            'titre' => $request->titre,
            'description' => $request->description,
            'mots_cles' => $request->mots_cles ?: null,
            'type' => $request->type,
            'catalogue' => $request->catalogue ?: null,
            'url' => $request->url ?: null,
            'ordre' => (int) ($request->ordre ?? 0),
            'actif' => $request->boolean('actif'),
        ];

        if ($request->hasFile('media')) {
            if ($formation->media_path) {
                Storage::disk('local')->delete($formation->media_path);
            }
            $data['media_path'] = $request->file('media')->store('formations/media', 'local');
        }
        if ($request->hasFile('fichier')) {
            if ($formation->fichier_path) {
                Storage::disk('local')->delete($formation->fichier_path);
            }
            $f = $request->file('fichier');
            $data['fichier_path'] = $f->store('formations/fichiers', 'local');
            $data['fichier_nom'] = $f->getClientOriginalName();
        }

        $formation->update($data);

        return redirect()->route('formations.index')->with('success', 'Formation / contenu mis à jour.');
    }

    public function destroy(Formation $formation)
    {
        $this->authorizeFormations();
        if ($formation->media_path) {
            Storage::disk('local')->delete($formation->media_path);
        }
        if ($formation->fichier_path) {
            Storage::disk('local')->delete($formation->fichier_path);
        }
        $formation->delete();
        return back()->with('success', 'Formation / contenu supprimé.');
    }

    /** Ajouter un catalogue (thème) — utilisé depuis la page Nouveau contenu. */
    public function storeCatalogue(Request $request)
    {
        $this->authorizeFormations();
        $request->validate([
            'label' => 'required|string|max:255',
        ]);
        $label = trim($request->label);
        $slug = FormationCatalogue::slugFromLabel($label);
        if (FormationCatalogue::where('slug', $slug)->exists()) {
            return back()->withErrors(['catalogue_label' => 'Un catalogue avec ce nom existe déjà.'])->withInput();
        }
        $maxOrdre = FormationCatalogue::max('ordre') ?? 0;
        FormationCatalogue::create([
            'slug' => $slug,
            'label' => $label,
            'ordre' => $maxOrdre + 1,
        ]);
        return back()->with('success', 'Catalogue « ' . $label . ' » ajouté.');
    }

    /** Supprimer un catalogue — les formations concernées passent en « sans catalogue ». */
    public function destroyCatalogue(int $catalogue)
    {
        $this->authorizeFormations();
        $cat = FormationCatalogue::findOrFail($catalogue);
        Formation::where('catalogue', $cat->slug)->update(['catalogue' => null]);
        $label = $cat->label;
        $cat->delete();
        return back()->with('success', 'Catalogue « ' . $label . ' » supprimé. Les contenus concernés n\'ont plus de thème.');
    }

    /** Afficher ou lire le média (photo / vidéo) uploadé. */
    public function media(Formation $formation)
    {
        if (! $formation->media_path || ! Storage::disk('local')->exists($formation->media_path)) {
            abort(404);
        }
        $path = Storage::disk('local')->path($formation->media_path);
        $mime = Storage::disk('local')->mimeType($formation->media_path);

        return response()->file($path, ['Content-Type' => $mime]);
    }

    /** Télécharger le fichier attaché à la formation. */
    public function fichier(Formation $formation)
    {
        if (! $formation->fichier_path || ! Storage::disk('local')->exists($formation->fichier_path)) {
            abort(404);
        }
        $nom = $formation->fichier_nom ?: basename($formation->fichier_path);

        return Storage::disk('local')->download($formation->fichier_path, $nom);
    }

    private function authorizeFormations(): void
    {
        if (! auth()->user()->canAddEntries()) {
            abort(403, 'Seuls les fondateurs, directeurs et manageurs peuvent ajouter ou modifier les formations.');
        }
    }
}
