<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\CreateurStatMensuelle;
use App\Models\Equipe;
use App\Models\FormationCatalogue;
use App\Models\RapportVendredi;
use App\Models\Regle;
use App\Models\ScoreFidelite;
use App\Models\ScoreIntegriteHistorique;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard : créateur = uniquement Jours / Heures / Diamants ; autres rôles = résumé périmètre.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Rôle créateur : ses données stream + historique par mois (fiche par user_id ou email, sync user_id si besoin)
        if ($user->isCreateur()) {
            $messageAgence = Regle::where('actif', true)->orderBy('ordre')->orderByDesc('updated_at')->first();
            $hasCatalogueTiktok = FormationCatalogue::where('slug', 'tiktok')->exists();

            $createurRecord = $user->getCreateurFiche();
            if ($createurRecord) {
                $createurRecord->load(['statsMensuelles', 'agent', 'equipe', 'ambassadeur']);
            }

            $moisDisponibles = collect();
            if ($createurRecord) {
                foreach ($createurRecord->statsMensuelles as $s) {
                    $moisDisponibles->push(['annee' => $s->annee, 'mois' => $s->mois, 'libelle' => $s->libelle]);
                }
                // Données actuelles sur la fiche = un "mois" possible même sans ligne d'historique
                $hasCurrent = $createurRecord->heures_mois !== null || $createurRecord->jours_mois !== null || $createurRecord->diamants !== null;
                $nowY = (int) now()->format('Y');
                $nowM = (int) now()->format('n');
                $noms = [1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                if ($hasCurrent && $moisDisponibles->where('annee', $nowY)->where('mois', $nowM)->isEmpty()) {
                    $moisDisponibles->prepend(['annee' => $nowY, 'mois' => $nowM, 'libelle' => ($noms[$nowM] ?? '').' '.$nowY]);
                }
                $moisDisponibles = $moisDisponibles->sortByDesc('annee')->sortByDesc('mois')->values();
            }

            $first = $moisDisponibles->first();
            $defAnnee = $first ? (int) $first['annee'] : (int) now()->format('Y');
            $defMois = $first ? (int) $first['mois'] : (int) now()->format('n');
            $annee = (int) $request->input('annee', $defAnnee);
            $mois = (int) $request->input('mois', $defMois);
            $valid = $moisDisponibles->contains(fn ($m) => (int) $m['annee'] === $annee && (int) $m['mois'] === $mois);
            if (! $valid) {
                $annee = $defAnnee;
                $mois = $defMois;
            }

            $statMensuelle = $createurRecord
                ? $createurRecord->statsMensuelles->firstWhere(fn ($s) => (int) $s->annee === $annee && (int) $s->mois === $mois)
                : null;
            if ($createurRecord && ! $statMensuelle) {
                $statMensuelle = CreateurStatMensuelle::where('createur_id', $createurRecord->id)
                    ->where('annee', $annee)
                    ->where('mois', $mois)
                    ->first();
            }

            if ($createurRecord && ! $statMensuelle && $annee === (int) now()->format('Y') && $mois === (int) now()->format('n')) {
                $jours = $createurRecord->jours_mois;
                $heures = $createurRecord->heures_mois;
                $diamants = $createurRecord->diamants;
            } else {
                $jours = $statMensuelle?->jours_stream;
                $heures = $statMensuelle?->heures_stream;
                $diamants = $statMensuelle?->diamants;
            }

            $scoreIntegrite = null;
            if ($createurRecord) {
                $lastScore = ScoreIntegriteHistorique::where('createur_id', $createurRecord->id)
                    ->orderByDesc('heure_modification')
                    ->value('score_consequent');
                $scoreIntegrite = $lastScore !== null ? (int) $lastScore : ScoreIntegriteHistorique::SCORE_MAX;
            }

            $scoreFideliteMois = null;
            if ($createurRecord) {
                $anneeMois = (int) now()->format('Y');
                $moisMois = (int) now()->format('n');
                $sf = ScoreFidelite::getOrCreateForCreateur($createurRecord->id, $anneeMois, $moisMois);
                $scoreFideliteMois = $sf->score;
            }

            $rapportVendrediManquant = $this->rapportVendrediManquant($user);

            return view('dashboard', [
                'stats' => null,
                'equipes' => collect(),
                'createurRecord' => $createurRecord,
                'messageAgence' => $messageAgence,
                'hasCatalogueTiktok' => $hasCatalogueTiktok,
                'moisDisponibles' => $moisDisponibles,
                'annee' => $annee,
                'mois' => $mois,
                'jours' => $jours,
                'heures' => $heures,
                'diamants' => $diamants,
                'scoreIntegrite' => $scoreIntegrite,
                'scoreFideliteMois' => $scoreFideliteMois,
                'rapportVendrediManquant' => $rapportVendrediManquant,
            ]);
        }

        $messageAgence = Regle::where('actif', true)->orderBy('ordre')->orderByDesc('updated_at')->first();
        $hasCatalogueTiktok = FormationCatalogue::where('slug', 'tiktok')->exists();

        $queryCreateurs = User::where('role', User::ROLE_CREATEUR);
        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        if ($user->isFondateurPrincipal()) {
            // Tous les créateurs (vue générale)
        } elseif ($equipeAgenceId !== null) {
            $queryCreateurs->where('equipe_id', $equipeAgenceId);
        } elseif ($user->isDirecteur() || $user->isSousDirecteur()) {
            $queryCreateurs->where('role', '!=', User::ROLE_FONDATEUR);
        } elseif ($user->isManageur() || $user->isSousManager() || $user->isAgent()) {
            $queryCreateurs->where('equipe_id', $user->equipe_id);
        } else {
            $queryCreateurs->where('id', $user->id);
        }

        if ($user->isFondateurPrincipal() && $equipeAgenceId === null) {
            $stats = [
                'createurs' => (clone $queryCreateurs)->count(),
                'equipes' => Equipe::count(),
                'users' => User::count(),
                'vues_totales' => 0,
                'followers_totaux' => 0,
            ];
            $equipes = Equipe::withCount(['membres as createurs_count' => function ($q) {
                $q->where('role', User::ROLE_CREATEUR);
            }])->with('manager')->get();
        } elseif ($equipeAgenceId !== null || (($user->isManageur() || $user->isSousManager() || $user->isAgent()) && $user->equipe_id)) {
            $equipeToShow = $equipeAgenceId ? Equipe::find($equipeAgenceId) : $user->equipe;
            $stats = [
                'createurs' => (clone $queryCreateurs)->count(),
                'equipes' => $equipeToShow ? 1 : 0,
                'vues_totales' => 0,
                'followers_totaux' => 0,
            ];
            $equipes = $equipeToShow
                ? collect([$equipeToShow->loadCount(['membres as createurs_count' => function ($q) {
                    $q->where('role', User::ROLE_CREATEUR);
                }])])
                : collect();
        } elseif ($user->isDirecteur() || $user->isSousDirecteur()) {
            $stats = [
                'createurs' => (clone $queryCreateurs)->count(),
                'equipes' => Equipe::count(),
                'users' => User::count(),
                'vues_totales' => 0,
                'followers_totaux' => 0,
            ];
            $equipes = Equipe::withCount(['membres as createurs_count' => function ($q) {
                $q->where('role', User::ROLE_CREATEUR);
            }])->with('manager')->get();
        } else {
            $stats = [
                'createurs' => (clone $queryCreateurs)->count(),
                'equipes' => 0,
                'vues_totales' => 0,
                'followers_totaux' => 0,
            ];
            $equipes = collect();
        }

        $rapportVendrediManquant = $this->rapportVendrediManquant($user);

        return view('dashboard', compact('stats', 'equipes') + [
            'createurRecord' => null,
            'messageAgence' => $messageAgence,
            'hasCatalogueTiktok' => $hasCatalogueTiktok,
            'scoreIntegrite' => null,
            'rapportVendrediManquant' => $rapportVendrediManquant,
        ]);
    }

    private function rapportVendrediManquant(User $user): bool
    {
        if (! $user->doitRemplirRapportVendredi()) {
            return false;
        }
        $annee = (int) now()->format('o');
        $semaine = (int) now()->format('W');

        return ! RapportVendredi::where('user_id', $user->id)
            ->where('annee', $annee)
            ->where('semaine', $semaine)
            ->exists();
    }
}
