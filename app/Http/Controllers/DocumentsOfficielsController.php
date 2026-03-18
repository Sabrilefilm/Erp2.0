<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use Illuminate\Http\Request;

class DocumentsOfficielsController extends Controller
{
    /**
     * Page unique Contrat et Règlement (sous Aide & informations).
     * Pour les créateurs : contrat détaillé, modification des infos (nom, email, tél), puis signature.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $createur = $user->isCreateur() ? Createur::where('user_id', $user->id)->first() : null;

        return view('documents-officiels.index', ['createur' => $createur]);
    }

    /**
     * Page 2 : Règlement intérieur. Le bloc « Tout accepter » est en bas de cette page uniquement.
     */
    public function reglement(Request $request)
    {
        $user = $request->user();
        $createur = $user->isCreateur() ? Createur::where('user_id', $user->id)->first() : null;

        return view('documents-officiels.reglement', ['createur' => $createur]);
    }

    /**
     * Mettre à jour les informations du créateur (nom, email, téléphone) avant signature.
     */
    public function updateInfo(Request $request)
    {
        $user = $request->user();
        if (! $user->isCreateur()) {
            abort(403, 'Seuls les créateurs peuvent modifier ces informations.');
        }

        $createur = Createur::where('user_id', $user->id)->first();
        if (! $createur) {
            return redirect()->route('documents-officiels.index')
                ->with('error', 'Fiche créateur introuvable.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
        ]);

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone') ?: null,
        ]);

        $createur->update([
            'nom' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        return redirect()->route('documents-officiels.index')
            ->with('success', 'Vos informations ont été enregistrées. Vous pouvez signer le contrat ci-dessous.');
    }

    /**
     * Enregistrer la signature électronique du contrat (créateur uniquement).
     */
    public function sign(Request $request)
    {
        $user = $request->user();
        if (! $user->isCreateur()) {
            abort(403, 'Seuls les créateurs peuvent signer le contrat.');
        }

        $createur = Createur::where('user_id', $user->id)->first();
        if (! $createur) {
            return redirect()->route('documents-officiels.index')
                ->with('error', 'Fiche créateur introuvable.');
        }

        if ($createur->contrat_signe_le) {
            return redirect()->route('documents-officiels.index')
                ->with('info', 'Vous avez déjà signé le contrat le ' . $createur->contrat_signe_le->format('d/m/Y à H:i') . '.');
        }

        $createur->update(['contrat_signe_le' => now()]);

        return redirect()->route('documents-officiels.index')
            ->with('success', 'Contrat signé avec succès. Vous pouvez télécharger le PDF ci-dessous.');
    }

    /**
     * Enregistrer l'acceptation du règlement intérieur (créateur uniquement).
     */
    public function acceptReglement(Request $request)
    {
        $user = $request->user();
        if (! $user->isCreateur()) {
            abort(403, 'Seuls les créateurs doivent accepter le règlement.');
        }

        $request->validate([
            'accept' => 'required|accepted',
        ], [
            'accept.required' => 'Vous devez accepter le règlement intérieur.',
            'accept.accepted' => 'Vous devez cocher la case pour accepter le règlement.',
        ]);

        $createur = Createur::where('user_id', $user->id)->first();
        if (! $createur) {
            return redirect()->route('documents-officiels.index')
                ->with('error', 'Fiche créateur introuvable.');
        }

        if ($createur->reglement_accepte_le) {
            return redirect()->route('documents-officiels.index')
                ->with('info', 'Vous avez déjà accepté le règlement le ' . $createur->reglement_accepte_le->format('d/m/Y à H:i') . '.');
        }

        $createur->update(['reglement_accepte_le' => now()]);

        return redirect()->route('documents-officiels.index')
            ->with('success', 'Règlement intérieur accepté. Vous avez accès à l\'application.');
    }

    /**
     * Signer le contrat ET accepter le règlement en une seule action (créateur uniquement).
     */
    public function acceptTout(Request $request)
    {
        $user = $request->user();
        if (! $user->isCreateur()) {
            abort(403, 'Seuls les créateurs doivent signer et accepter les documents.');
        }

        $request->validate([
            'accept_contrat' => 'required|accepted',
            'accept_reglement' => 'required|accepted',
        ], [
            'accept_contrat.required' => 'Vous devez accepter le contrat.',
            'accept_contrat.accepted' => 'Vous devez cocher l\'acceptation du contrat.',
            'accept_reglement.required' => 'Vous devez accepter le règlement intérieur.',
            'accept_reglement.accepted' => 'Vous devez cocher l\'acceptation du règlement.',
        ]);

        $createur = Createur::where('user_id', $user->id)->first();
        if (! $createur) {
            return redirect()->route('documents-officiels.index')
                ->with('error', 'Fiche créateur introuvable.');
        }

        $updates = [];
        if (! $createur->contrat_signe_le) {
            $updates['contrat_signe_le'] = now();
        }
        if (! $createur->reglement_accepte_le) {
            $updates['reglement_accepte_le'] = now();
        }

        if (! empty($updates)) {
            $createur->update($updates);
        }

        return redirect()->route('documents-officiels.reglement')
            ->with('success', 'Contrat signé et règlement accepté. Vous avez accès à l\'application.');
    }
}
