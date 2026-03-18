<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\Recompense;
use App\Models\User;
use App\Notifications\RecompenseAttribueeNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecompenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Liste et montant : selon le périmètre (tout le monde voit sa page avec son périmètre) — avec supprimées pour afficher "Supprimé"
        $query = Recompense::with(['createur.equipe', 'attribuePar'])->withTrashed()->latest();
        if (! $user->canAttribuerOuRecupererRecompense()) {
            $equipeAgenceId = $user->scopeToAgenceEquipeId();
            if ($equipeAgenceId !== null) {
                $query->whereHas('createur', fn ($q) => $q->where('equipe_id', $equipeAgenceId));
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $query->whereHas('createur', fn ($q) => $q->where('equipe_id', $user->equipe_id));
            } elseif ($user->isAgent()) {
                $query->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id));
            } elseif ($user->isCreateur()) {
                $query->whereHas('createur', fn ($q) => $q->where('user_id', $user->id)->orWhere('email', $user->email));
            }
        }

        $budgetTotal = (float) config('recompenses.budget_total', 15);
        $montantUtilise = (float) Recompense::withoutTrashed()->sum('montant');
        $montantRestant = max(0, $budgetTotal - $montantUtilise);
        $recompenses = $query->paginate(20)->withQueryString();

        // Créateurs pour le formulaire d'attribution (uniquement fondateur)
        $createurs = collect();
        if ($user->canAttribuerOuRecupererRecompense()) {
            $createurs = Createur::query()->whereNotNull('user_id')->orderBy('nom')->get(['id', 'nom', 'pseudo_tiktok']);
        }

        // Récompenses en attente de choix : celles dont le créateur est lié à l'utilisateur connecté (user_id ou email), quel que soit le rôle
        $recompensesEnAttenteChoix = Recompense::with(['createur', 'attribuePar'])
            ->where('statut', Recompense::STATUT_EN_ATTENTE_CHOIX)
            ->whereHas('createur', fn ($q) => $q->where('user_id', $user->id)->orWhere('email', $user->email))
            ->latest()
            ->get();

        $montantAReceptionner = (float) $recompensesEnAttenteChoix->sum('montant');

        return view('recompenses.index', compact('recompenses', 'createurs', 'budgetTotal', 'montantUtilise', 'montantRestant', 'recompensesEnAttenteChoix', 'montantAReceptionner'));
    }

    /** Codes banque français (5 chiffres après FR76 dans l'IBAN) => nom de la banque. Fallback quand l'API externe est indisponible. */
    private static function banquesFranceParCode(): array
    {
        return [
            '10007' => 'Banque de France',
            '10207' => 'Crédit Agricole',
            '12507' => 'Crédit Agricole',
            '12807' => 'Crédit Agricole',
            '13507' => 'Caisse d\'Épargne',
            '13807' => 'Caisse d\'Épargne',
            '16807' => 'Crédit Agricole',
            '17515' => 'BNP Paribas',
            '18207' => 'Crédit Agricole',
            '18307' => 'Crédit Agricole',
            '18407' => 'Crédit Agricole',
            '18507' => 'Crédit Agricole',
            '18707' => 'Crédit Agricole',
            '18907' => 'Crédit Agricole',
            '19107' => 'Crédit Agricole',
            '19807' => 'Crédit Agricole',
            '20041' => 'BNP Paribas',
            '30003' => 'Société Générale',
            '30004' => 'BNP Paribas',
            '30006' => 'Société Générale',
            '30076' => 'BNP Paribas',
            '30588' => 'Société Générale',
            '30669' => 'Société Générale',
            '30948' => 'Société Générale',
            '40001' => 'Crédit Mutuel',
            '42559' => 'CIC',
            '42616' => 'CIC',
            '43205' => 'CIC',
            '43519' => 'CIC',
            '43733' => 'CIC',
            '43974' => 'CIC',
            '44118' => 'CIC',
            '44411' => 'CIC',
            '44539' => 'CIC',
            '44739' => 'CIC',
            '45069' => 'CIC',
            '45448' => 'CIC',
            '46114' => 'CIC',
            '46506' => 'CIC',
            '46608' => 'CIC',
            '47522' => 'CIC',
            '47833' => 'CIC',
            '48339' => 'CIC',
            '48448' => 'CIC',
            '48591' => 'CIC',
            '48637' => 'CIC',
            '48787' => 'CIC',
            '48868' => 'CIC',
            '48923' => 'CIC',
            '49468' => 'CIC',
            '50549' => 'Banque Populaire',
            '50850' => 'Banque Populaire',
            '51207' => 'Crédit Agricole',
            '10278' => 'Crédit Agricole',
            '13808' => 'Caisse d\'Épargne',
            '16808' => 'Crédit Agricole',
            '18408' => 'Crédit Agricole',
            '17118' => 'La Banque Postale',
            '17519' => 'BNP Paribas',
            '20041' => 'BNP Paribas',
            '13329' => 'LCL',
            '14569' => 'LCL',
            '30784' => 'HSBC France',
        ];
    }

    /** Retourne le nom de la banque à partir de l'IBAN (API OpenIBAN + fallback codes France). */
    public function ibanBanque(Request $request)
    {
        $iban = preg_replace('/\s+/', '', strtoupper((string) $request->query('iban', '')));
        if (strlen($iban) < 15) {
            return response()->json(['bank' => null, 'error' => 'IBAN trop court']);
        }

        $bank = null;

        try {
            $response = Http::timeout(4)->get('https://openiban.com/validate/'.$iban.'?getBIC=true');
            $data = $response->json();
            if (! empty($data['bankData']['name']) && trim((string) $data['bankData']['name']) !== '') {
                $bank = trim((string) $data['bankData']['name']);
            }
        } catch (\Throwable $e) {
            // On continue pour tenter le fallback
        }

        if (! $bank && str_starts_with($iban, 'FR') && strlen($iban) >= 9) {
            $codeBanque = substr($iban, 4, 5);
            $banques = self::banquesFranceParCode();
            $bank = $banques[$codeBanque] ?? null;
        }

        if ($bank) {
            return response()->json(['bank' => $bank, 'valid' => true]);
        }

        return response()->json(['bank' => null, 'error' => 'Banque non trouvée pour cet IBAN. Saisissez le nom manuellement.']);
    }

    public function store(Request $request)
    {
        if (! $request->user()->canAttribuerOuRecupererRecompense()) {
            abort(403, 'Seul le fondateur peut attribuer une récompense.');
        }
        $request->validate([
            'createur_id' => 'required|exists:createurs,id',
            'montant' => 'required|numeric|min:0',
            'raison' => 'nullable|string|max:500',
        ], [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant doit être au moins 0 €.',
        ]);

        $recompense = Recompense::create([
            'createur_id' => $request->createur_id,
            'type' => null,
            'montant' => $request->montant,
            'raison' => $request->raison,
            'attribue_par' => $request->user()->id,
            'statut' => Recompense::STATUT_EN_ATTENTE_CHOIX,
        ]);

        $recompense->load('createur');
        $createur = $recompense->createur;
        $beneficiaire = $createur?->user ?? ($createur && $createur->email ? User::where('email', $createur->email)->first() : null);
        if ($beneficiaire) {
            $beneficiaire->notify(new RecompenseAttribueeNotification($recompense));
        }

        return back()->with('success', 'Récompense attribuée. Le créateur pourra choisir le mode de réception (virement, TikTok ou carte cadeau).');
    }

    /** Le créateur choisit le mode de réception (virement, TikTok ou carte cadeau) pour une récompense en attente. */
    public function choisirType(Request $request, Recompense $recompense)
    {
        $user = $request->user();
        if (! $recompense->isEnAttenteChoix()) {
            return back()->with('error', 'Cette récompense a déjà un mode de réception.');
        }
        $createur = $recompense->createur;
        $estLeCreateur = $createur && (
            ($createur->user_id && (int) $createur->user_id === (int) $user->id)
            || (strtolower((string) $createur->email) === strtolower((string) $user->email))
        );
        if (! $estLeCreateur) {
            abort(403, 'Seul le créateur concerné peut choisir le mode de réception.');
        }

        $rules = [
            'type' => 'required|string|in:'.implode(',', array_keys(Recompense::TYPES)),
        ];
        if ($request->type === Recompense::TYPE_TIKTOK) {
            $dateMin = now()->startOfDay()->format('Y-m-d');
            $dateMax = now()->addMonths(2)->format('Y-m-d');
            $rules['date_cadeau_tiktok'] = 'required|date|after_or_equal:'.$dateMin.'|before_or_equal:'.$dateMax;
            $rules['heure_cadeau_tiktok'] = 'required|string|max:5';
        }
        if ($request->type === Recompense::TYPE_VIREMENT) {
            $rules['rib_nom'] = 'required|string|max:120';
            $rules['rib_prenom'] = 'required|string|max:120';
            $rules['rib_iban'] = 'required|string|max:50';
            $rules['rib_banque'] = 'required|string|max:120';
            $rules['rib_confirme'] = 'required|accepted';
        }
        if ($request->type === Recompense::TYPE_CARTE_CADEAU) {
            $rules['type_carte_cadeau'] = 'required|string|in:'.implode(',', array_keys(Recompense::TYPES_CARTE_CADEAU));
            $rules['montant_carte_cadeau'] = 'required|numeric|in:'.implode(',', Recompense::MONTANTS_CARTE_CADEAU);
            $rules['quantite_carte_cadeau'] = 'required|integer|min:1';
        }
        $messages = [
            'date_cadeau_tiktok.after_or_equal' => 'La date du cadeau TikTok ne peut pas être dans le passé. Choisissez aujourd\'hui ou une date ultérieure.',
            'date_cadeau_tiktok.before_or_equal' => 'La date du cadeau TikTok ne peut pas être plus de 2 mois après aujourd\'hui.',
            'type_carte_cadeau.required' => 'Veuillez choisir une carte cadeau (Multi-Enseignes, Expedia, Zalando, etc.).',
            'montant_carte_cadeau.required' => 'Choisissez le montant de la carte cadeau.',
            'montant_carte_cadeau.in' => 'Le montant choisi n\'est pas valide.',
            'quantite_carte_cadeau.required' => 'Choisissez la quantité.',
        ];
        $request->validate($rules, $messages);

        if ($request->type === Recompense::TYPE_CARTE_CADEAU) {
            $montantCc = (float) $request->montant_carte_cadeau;
            $qte = (int) $request->quantite_carte_cadeau;
            $montantAttendu = (float) $recompense->montant;
            if ($montantCc > $montantAttendu || $montantCc * $qte != $montantAttendu) {
                return back()->withErrors([
                    'quantite_carte_cadeau' => 'Le montant × la quantité doit correspondre à votre solde ('.number_format($montantAttendu, 2, ',', ' ').' €).',
                ])->withInput();
            }
        }

        $data = [
            'type' => $request->type,
            'statut' => 'attribue',
        ];
        if ($request->type === Recompense::TYPE_TIKTOK) {
            $data['date_cadeau_tiktok'] = $request->date_cadeau_tiktok;
            $data['heure_cadeau_tiktok'] = $request->heure_cadeau_tiktok;
        }
        if ($request->type === Recompense::TYPE_VIREMENT) {
            $data['rib_nom'] = $request->rib_nom;
            $data['rib_prenom'] = $request->rib_prenom;
            $data['rib_iban'] = $request->rib_iban;
            $data['rib_banque'] = $request->rib_banque;
            $data['rib_confirme'] = true;
        }
        if ($request->type === Recompense::TYPE_CARTE_CADEAU) {
            $data['type_carte_cadeau'] = $request->type_carte_cadeau;
            $data['montant_carte_cadeau'] = (float) $request->montant_carte_cadeau;
            $data['quantite_carte_cadeau'] = (int) $request->quantite_carte_cadeau;
        }

        $delaiSecondes = (int) config('recompenses.facture_delai_secondes', 5);
        $data['facture_disponible_at'] = now()->addSeconds($delaiSecondes);

        $recompense->update($data);

        return back()->with('success', 'Mode de réception enregistré : '.(Recompense::TYPES[$request->type] ?? $request->type).'. La facture sera disponible dans '.$delaiSecondes.' secondes.');
    }

    /** Modifier une récompense (saisir le code carte cadeau) — réservé au fondateur. */
    public function update(Request $request, Recompense $recompense)
    {
        if (! $request->user()->canAttribuerOuRecupererRecompense()) {
            abort(403, 'Seul le fondateur peut modifier ces informations.');
        }

        if ($recompense->type === Recompense::TYPE_CARTE_CADEAU) {
            $request->validate(['code_cadeau' => 'nullable|string|max:255']);
            $recompense->update(['code_cadeau' => $request->filled('code_cadeau') ? $request->code_cadeau : null]);
            return back()->with('success', 'Code carte cadeau enregistré. Le créateur pourra le retrouver sur sa facture.');
        }

        return back()->with('info', 'Aucune modification.');
    }

    /** Page détail d'une récompense : infos, RIB (affichage au clic), refus avec motif (fondateur). */
    public function show(Request $request, Recompense $recompense)
    {
        $user = $request->user();
        $canView = $user->canAttribuerOuRecupererRecompense();
        if (! $canView) {
            $createur = $recompense->createur;
            $canView = $createur && (
                ($createur->user_id && (int) $createur->user_id === (int) $user->id)
                || (strtolower((string) $createur->email) === strtolower((string) $user->email))
            );
        }
        if (! $canView) {
            abort(403, 'Vous ne pouvez pas voir cette récompense.');
        }
        $recompense->load(['createur', 'attribuePar']);

        return view('recompenses.show', ['recompense' => $recompense]);
    }

    /** Refuser une récompense avec motif — réservé au fondateur. */
    public function refuser(Request $request, Recompense $recompense)
    {
        if (! $request->user()->canAttribuerOuRecupererRecompense()) {
            abort(403, 'Seul le fondateur peut refuser une récompense.');
        }
        $request->validate([
            'motif_refus' => 'required|string|max:2000',
        ], [
            'motif_refus.required' => 'Indiquez le motif du refus.',
        ]);
        $recompense->update([
            'statut' => Recompense::STATUT_REFUSEE,
            'motif_refus' => $request->motif_refus,
        ]);

        return redirect()->route('recompenses.index')->with('success', 'Récompense refusée. Le créateur pourra consulter le motif.');
    }

    /** Télécharger la facture — fondateur ou créateur concerné. Disponible quelques secondes après le choix du mode (anti-surcharge). */
    public function facturePdf(Recompense $recompense)
    {
        if ($recompense->isEnAttenteChoix()) {
            return back()->with('info', 'La facture sera disponible une fois que le créateur aura choisi le mode de réception.');
        }
        if (! $recompense->factureEstDisponible()) {
            $secondes = $recompense->secondesRestantesFacture();
            return back()->with('info', 'La facture sera disponible dans '.$secondes.' seconde'.($secondes > 1 ? 's' : '').'.');
        }
        $user = auth()->user();
        $canDownload = $user->canAttribuerOuRecupererRecompense();
        if (! $canDownload) {
            $createur = $recompense->createur;
            $canDownload = $createur && (
                ($createur->user_id && (int) $createur->user_id === (int) $user->id)
                || (strtolower((string) $createur->email) === strtolower((string) $user->email))
            );
        }
        if (! $canDownload) {
            abort(403, 'Vous ne pouvez pas télécharger cette facture.');
        }
        $recompense->load(['createur', 'attribuePar']);
        $factureConfig = config('recompenses.facture', []);
        $logoPath = ! empty($factureConfig['logo']) && is_file($factureConfig['logo'])
            ? $factureConfig['logo']
            : public_path('images/logo-unions-agency.png');
        $logoDataUri = null;
        if (is_file($logoPath)) {
            $mime = mime_content_type($logoPath);
            if ($mime && in_array($mime, ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'], true)) {
                $logoDataUri = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($logoPath));
            }
        }

        $pdf = Pdf::loadView('recompenses.facture-pdf', [
            'recompense' => $recompense,
            'typeLabels' => Recompense::TYPES,
            'logoDataUri' => $logoDataUri,
            'facture' => $factureConfig,
        ])->setPaper('a4', 'portrait');

        $filename = 'facture-recompense-' . $recompense->id . '-' . $recompense->createur->nom . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '-', $filename);

        return $pdf->download($filename);
    }

    /** Supprimer une récompense — réservé au fondateur. */
    public function destroy(Request $request, Recompense $recompense)
    {
        if (! $request->user()->canAttribuerOuRecupererRecompense()) {
            abort(403, 'Seul le fondateur peut supprimer une récompense.');
        }
        $recompense->delete();

        return back()->with('success', 'Récompense supprimée.');
    }
}
