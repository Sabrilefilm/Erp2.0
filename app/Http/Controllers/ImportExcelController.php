<?php

namespace App\Http\Controllers;

use App\Exports\CreateursDonneesExport;
use App\Exports\CreateursTemplateExport;
use App\Support\FrenchExceptionMessage;
use App\Support\HeuresHelper;
use App\Imports\CreateursImport;
use App\Models\Createur;
use App\Models\CreateurStatMensuelle;
use App\Models\Equipe;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportExcelController extends Controller
{
    /**
     * Affichage du formulaire d'import (Fondateur uniquement — route déjà protégée par middleware).
     * Si ?show_log=ID est présent, affiche le journal de cet import (en plus du dernier en session).
     */
    public function index(Request $request)
    {
        $logs = ImportLog::with('user')->latest()->take(50)->get();
        $showLog = null;
        if ($request->filled('show_log')) {
            $showLog = ImportLog::find($request->show_log);
        }

        return view('import.index', compact('logs', 'showLog'));
    }

    /**
     * Supprimer un enregistrement de l'historique des imports (Fondateur uniquement).
     */
    public function destroyLog(ImportLog $import_log)
    {
        $import_log->delete();
        return redirect()->route('import.index')->with('success', 'Entrée d\'historique supprimée.');
    }

    /**
     * Traitement de l'import Excel. Transaction + rollback si erreur. Journalisation obligatoire.
     * Si requête AJAX (wantsJson), retourne JSON avec tout le détail pour affichage direct sur la page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier Excel.',
            'fichier.mimes' => 'Le fichier doit être au format .xlsx ou .xls',
        ]);

        $file = $request->file('fichier');
        $filename = $file->getClientOriginalName();

        try {
            DB::beginTransaction();

            $annee = (int) $request->input('annee', now()->year);
            $mois = (int) $request->input('mois', now()->month);
            $import = new CreateursImport($annee, $mois);
            Excel::import($import, $file);

            $imported = $import->getImported();
            $errors = $import->getErrors();
            $logLines = $import->getLogLines();

            // 0 lignes importées = avertissement (usernames ou colonnes à vérifier)
            $zeroImported = ($imported === 0 && $errors === 0);
            if ($zeroImported) {
                $logLines[] = ['type' => 'solution', 'line' => 0, 'msg' => '💡 Vérifiez que la colonne C contient le nom d\'utilisateur exact (comme dans l\'app) et que le mois choisi est le bon. Consultez « Corriger heures et jours » pour voir les données actuelles.'];
            }

            ImportLog::create([
                'user_id' => $request->user()->id,
                'fichier' => $filename,
                'statut' => $errors > 0 ? 'partiel' : ($zeroImported ? 'partiel' : 'succes'),
                'lignes_importees' => $imported,
                'lignes_erreur' => $errors,
                'message' => $import->getErrorMessage() ?: null,
                'log_detail' => $logLines,
            ]);

            DB::commit();

            $nomsMois = [1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            $libelleMois = ($nomsMois[$mois] ?? $mois) . ' ' . $annee;
            if ($zeroImported) {
                $message = "Aucune ligne importée. Vérifiez la colonne C (nom d'utilisateur) et le mois sélectionné.";
                $status = 'warning';
            } elseif ($errors > 0) {
                $message = "Import terminé avec {$imported} lignes importées et {$errors} erreur(s).";
                $status = 'warning';
            } else {
                $message = "{$imported} ligne(s) importée(s) pour {$libelleMois}. Heures, jours et diamants sont à jour.";
                $status = 'success';
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'lignes_importees' => $imported,
                    'lignes_erreur' => $errors,
                    'fichier' => $filename,
                    'import_log' => $logLines,
                    'solution' => $zeroImported ? 'Vérifiez la colonne C (nom d\'utilisateur exact) et le mois choisi. Consultez « Corriger heures et jours » pour voir les données.' : null,
                ]);
            }

            return back()
                ->with($status, $message)
                ->with('import_log', $logLines);
        } catch (\Throwable $e) {
            DB::rollBack();

            $messageFr = FrenchExceptionMessage::getMessage($e);
            $solution = FrenchExceptionMessage::getSolution($e);
            $logLines = [['type' => 'error', 'line' => 0, 'msg' => 'Erreur : '.$messageFr]];
            if ($solution) {
                $logLines[] = ['type' => 'solution', 'line' => 0, 'msg' => '💡 Solution : '.$solution];
            }
            ImportLog::create([
                'user_id' => $request->user()->id,
                'fichier' => $filename,
                'statut' => 'echec',
                'lignes_importees' => 0,
                'lignes_erreur' => 0,
                'message' => $messageFr,
                'log_detail' => $logLines,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erreur lors de l\'import : '.$messageFr,
                    'solution' => $solution,
                    'lignes_importees' => 0,
                    'lignes_erreur' => 0,
                    'fichier' => $filename,
                    'import_log' => $logLines,
                ], 422);
            }

            return back()
                ->with('error', 'Erreur lors de l\'import : '.$messageFr)
                ->with('import_solution', $solution)
                ->with('import_log', $logLines);
        }
    }

    /**
     * Téléchargement du modèle Excel (Fondateur uniquement).
     * Structure : colonnes C (username), H (diamants), N (heures), O (jours).
     */
    public function template(): BinaryFileResponse
    {
        return Excel::download(new CreateursTemplateExport, 'modele_import_createurs.xlsx');
    }

    /**
     * Export Excel des créateurs avec Heures, Jours et Diamants (même filtre que Corriger heures et jours).
     */
    public function exportDonnees(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        $query = Createur::with(['user:id,name,username', 'equipe:id,nom'])
            ->whereNotNull('user_id')
            ->orderBy('nom');

        if ($user->estFondateurSousAgence() && $user->equipe_id) {
            $query->where('equipe_id', $user->equipe_id);
        } elseif ($user->isFondateurPrincipal() && $request->filled('equipe_id')) {
            $query->where('equipe_id', (int) $request->equipe_id);
        }

        $createurs = $query->get();
        $export = new CreateursDonneesExport($createurs);

        $filename = 'createurs_heures_jours_diamants_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download($export, $filename);
    }

    /** Objectif mensuel (validation) : 7 jours, 16h, 1000 diamants. */
    private const OBJECTIF_JOURS = 7;
    private const OBJECTIF_HEURES = 16;
    private const OBJECTIF_DIAMANTS = 1000;

    /**
     * Page de correction des heures, jours et diamants (en cas d'erreur Excel). Fondateur uniquement.
     * Tri : objectif atteint en haut, puis par score décroissant (ceux avec le moins en bas).
     * Filtre : tous | objectif_atteint | a_completer.
     */
    public function corrigerHeuresJours(Request $request)
    {
        $user = $request->user();
        $query = Createur::with(['user:id,name,username', 'equipe:id,nom'])
            ->whereNotNull('user_id');

        if ($user->estFondateurSousAgence() && $user->equipe_id) {
            $query->where('equipe_id', $user->equipe_id);
        } elseif ($user->isFondateurPrincipal() && $request->filled('equipe_id')) {
            $query->where('equipe_id', (int) $request->equipe_id);
        }

        $createurs = $query->get();

        // Objectif atteint = 7 jours, 16h, 1000 diamants
        $createurs = $createurs->sortByDesc(function (Createur $c) {
            $jours = (int) ($c->jours_mois ?? 0);
            $heures = (float) ($c->heures_mois ?? 0);
            $diamants = (int) ($c->diamants ?? 0);
            $objectifAtteint = $jours >= self::OBJECTIF_JOURS
                && $heures >= self::OBJECTIF_HEURES
                && $diamants >= self::OBJECTIF_DIAMANTS;
            $score = $heures + $jours * 10 + $diamants / 100;
            return [$objectifAtteint ? 1 : 0, $score];
        })->values();

        $filtre = $request->get('filtre', 'tous');
        if ($filtre === 'objectif_atteint') {
            $createurs = $createurs->filter(function (Createur $c) {
                return ((int) ($c->jours_mois ?? 0)) >= self::OBJECTIF_JOURS
                    && ((float) ($c->heures_mois ?? 0)) >= self::OBJECTIF_HEURES
                    && ((int) ($c->diamants ?? 0)) >= self::OBJECTIF_DIAMANTS;
            })->values();
        } elseif ($filtre === 'a_completer') {
            $createurs = $createurs->filter(function (Createur $c) {
                return ((int) ($c->jours_mois ?? 0)) < self::OBJECTIF_JOURS
                    || ((float) ($c->heures_mois ?? 0)) < self::OBJECTIF_HEURES
                    || ((int) ($c->diamants ?? 0)) < self::OBJECTIF_DIAMANTS;
            })->values();
        }

        $equipes = $user->isFondateurPrincipal() ? Equipe::orderBy('nom')->get(['id', 'nom']) : collect();

        return view('import.corriger-heures-jours', [
            'createurs' => $createurs,
            'equipes' => $equipes,
            'equipeFilter' => $request->filled('equipe_id') ? (int) $request->equipe_id : null,
            'filtre' => $filtre,
            'objectifJours' => self::OBJECTIF_JOURS,
            'objectifHeures' => self::OBJECTIF_HEURES,
            'objectifDiamants' => self::OBJECTIF_DIAMANTS,
        ]);
    }

    /**
     * Enregistrement des corrections heures / jours / diamants.
     */
    public function mettreAJourHeuresJours(Request $request)
    {
        $user = $request->user();
        $createursData = $request->input('createurs', []);

        $rules = [
            'createurs' => 'array',
            'createurs.*.heures_mois' => 'nullable|string', // "7h30", "7.5" → parsé via HeuresHelper
            'createurs.*.jours_mois' => 'nullable|numeric|min:0|max:31', // accepte 7 ou 7.5, stocké en entier
            'createurs.*.diamants' => 'nullable|integer|min:0',
        ];
        $request->validate($rules, [
            'createurs.*.jours_mois.max' => 'Les jours ne peuvent pas dépasser 31.',
        ]);

        $allowedIds = Createur::query()
            ->whereNotNull('user_id')
            ->when($user->estFondateurSousAgence() && $user->equipe_id, fn ($q) => $q->where('equipe_id', $user->equipe_id))
            ->pluck('id')
            ->all();

        $updated = 0;
        foreach ($createursData as $id => $data) {
            $id = (int) $id;
            if (! in_array($id, $allowedIds, true)) {
                continue;
            }
            $createur = Createur::find($id);
            if (! $createur) {
                continue;
            }
            $heuresRaw = $data['heures_mois'] ?? null;
            $heures = $heuresRaw !== null && $heuresRaw !== '' ? HeuresHelper::parse(is_string($heuresRaw) ? $heuresRaw : (string) $heuresRaw) : 0.0;
            $joursRaw = $data['jours_mois'] ?? null;
            $jours = $joursRaw !== null && $joursRaw !== '' ? (int) min(31, max(0, (int) round((float) $joursRaw))) : 0;
            $diamants = isset($data['diamants']) && $data['diamants'] !== '' ? (int) $data['diamants'] : 0;
            if ($heures > 744) {
                $heures = 744.0;
            }
            $createur->update([
                'heures_mois' => $heures,
                'jours_mois' => $jours,
                'diamants' => $diamants,
                'date_import' => now(),
            ]);
            // Synchroniser l'historique mensuel (mois en cours) pour que dashboard et fiche affichent les mêmes valeurs
            $nowY = (int) now()->format('Y');
            $nowM = (int) now()->format('n');
            CreateurStatMensuelle::updateOrCreate(
                [
                    'createur_id' => $createur->id,
                    'annee' => $nowY,
                    'mois' => $nowM,
                ],
                [
                    'heures_stream' => $heures,
                    'jours_stream' => $jours,
                    'diamants' => $diamants,
                ]
            );
            $updated++;
        }

        $params = [];
        if ($request->filled('equipe_id')) {
            $params['equipe_id'] = $request->equipe_id;
        }
        if ($request->filled('filtre')) {
            $params['filtre'] = $request->filtre;
        }
        return redirect()
            ->route('import.corriger-heures-jours', $params)
            ->with('success', $updated > 0 ? "{$updated} créateur(s) mis à jour." : 'Aucune modification enregistrée.');
    }
}
