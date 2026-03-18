<?php

namespace App\Http\Controllers;

use App\Models\Createur;
use App\Models\CreateurAdverse;
use App\Models\DemandeMatch;
use App\Models\Equipe;
use App\Models\Planning;
use App\Models\ScoreFidelite;
use App\Models\User;
use App\Console\Commands\SupprimerMatchsPasses;
use App\Notifications\DemandeMatchNotification;
use App\Notifications\MatchProgrammeNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public const MATCH_TYPES = [
        'match_off' => 'Match officiel',
        'match_anniversaire' => 'Anniversaire',
        'match_depannage' => 'Dépannage',
        'match_tournoi' => 'Tournoi',
        'match_agence' => 'Agence',
    ];

    /** Catalogue Faucheuse Agency (Match Partenaire) = créateurs dont l'agence contient "Faucheuse" dans le nom */
    public const VUE_PARTENAIRE = 'partenaire';
    /** Catalogue Unions Agency (Match Unions) = créateurs des autres agences (nom d'équipe sans "Faucheuse") */
    public const VUE_UNIONS = 'unions';
    public const VUE_AUJOURDHUI = 'aujourdhui';

    public function index(Request $request)
    {
        // Nettoyer les matchs passés (date < aujourd’hui) si le cron n’a pas tourné
        SupprimerMatchsPasses::nettoyerMatchsPasses();

        $user = $request->user();
        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        $vue = $request->get('vue', $this->defaultVue($user));

        // Si "Aujourd'hui" : forcer la période du jour
        $from = $request->get('from');
        $to = $request->get('to');
        if ($vue === self::VUE_AUJOURDHUI) {
            $from = now()->startOfDay()->format('Y-m-d');
            $to = now()->addDays(6)->startOfDay()->format('Y-m-d'); // matchs à venir : 7 jours (aujourd'hui inclus)
        } else {
            // Par défaut : mois précédent → mois suivant pour avoir des matchs visibles
            if (! $request->filled('from')) {
                $from = now()->subMonth()->startOfMonth()->format('Y-m-d');
            }
            if (! $request->filled('to')) {
                $to = now()->addMonth()->endOfMonth()->format('Y-m-d');
            }
        }

        $query = Planning::with(['createur.equipe', 'creePar', 'updatedPar'])
            ->whereIn('type', array_keys(self::MATCH_TYPES));

        // Équipe du créateur (pour partenaire) — calculée pour tous
        $createurEquipeId = null;
        if ($user->isCreateur()) {
            $maFiche = Createur::where('email', $user->email)->orWhere('user_id', $user->id)->first();
            $createurEquipeId = $maFiche?->equipe_id;
        }

        // Filtre selon la vue : tous les rôles (y compris Créateur) voient selon l’onglet
        if ($vue === self::VUE_AUJOURDHUI) {
            // Aujourd'hui = matchs à venir sur 7 jours, 2 agences
        } elseif ($vue === self::VUE_UNIONS) {
            // Catalogue Unions Agency uniquement : exclure Faucheuse, afficher toute la liste Unions
            $query->whereDoesntHave('createur', fn ($q) => $q->faucheuseAgency());
            $unionsCatalogue = fn ($q) => $q->unionsAgency();
            $unionsEquipe = fn ($equipeId) => function ($q) use ($equipeId) {
                $q->where('equipe_id', $equipeId)->unionsAgency();
            };
            if ($user->isCreateur()) {
                if ($createurEquipeId !== null) {
                    $query->whereHas('createur', fn ($q) => $q->where('equipe_id', $createurEquipeId)->unionsAgency());
                } else {
                    $query->whereHas('createur', fn ($q) => $q->where(function ($q2) use ($user) {
                        $q2->where('email', $user->email)->orWhere('user_id', $user->id);
                    })->unionsAgency());
                }
            } elseif ($user->isAgent()) {
                $query->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id)->unionsAgency());
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $query->whereHas('createur', $unionsEquipe($user->equipe_id));
            } elseif ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
                // Fondateur / Directeur / Sous-directeur : toute la liste Unions, ou filtrée par équipe si sélectionnée
                if ($request->filled('equipe_id')) {
                    $query->whereHas('createur', $unionsEquipe($request->equipe_id));
                } else {
                    $query->whereHas('createur', $unionsCatalogue);
                }
            } elseif ($equipeAgenceId !== null) {
                // Fondateur d'agence : matchs de son agence uniquement
                $query->whereHas('createur', $unionsEquipe($equipeAgenceId));
            } else {
                // Autres rôles (ex. Ambassadeur) : toute la liste Unions
                $query->whereHas('createur', $unionsCatalogue);
            }
        } elseif ($vue === self::VUE_PARTENAIRE) {
            // Catalogue Faucheuse Agency : prénom/nom avec "Faucheuse"
            $partenaireEquipe = fn ($equipeId) => function ($q) use ($equipeId) {
                $q->where('equipe_id', $equipeId)->faucheuseAgency();
            };
            $partenaireCatalogue = fn ($q) => $q->faucheuseAgency();
            if ($user->isCreateur()) {
                // Créateur : Match partenaire = uniquement les match off Faucheuse Agency (catalogue partenaire)
                $query->whereHas('createur', fn ($q) => $q->faucheuseAgency());
            } else {
                if ($user->isAgent()) {
                    $query->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id)->faucheuseAgency());
                } elseif ($equipeAgenceId !== null) {
                    $query->whereHas('createur', $partenaireEquipe($equipeAgenceId));
                } elseif ($user->isManageur() || $user->isSousManager()) {
                    $query->whereHas('createur', $partenaireEquipe($user->equipe_id));
                } elseif ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
                    if ($request->filled('equipe_id')) {
                        $query->whereHas('createur', $partenaireEquipe($request->equipe_id));
                    } else {
                        $query->whereHas('createur', $partenaireCatalogue);
                    }
                }
            }
        } else {
            // Fallback (page sans vue) : créateur = son équipe ou ses matchs
            if ($user->isCreateur()) {
                if ($createurEquipeId !== null) {
                    $query->whereHas('createur', fn ($q) => $q->where('equipe_id', $createurEquipeId));
                } else {
                    $query->whereHas('createur', fn ($q) => $q->where('email', $user->email)->orWhere('user_id', $user->id));
                }
            }
        }

        if ($from !== null && $from !== '') {
            $query->where('date', '>=', $from);
        } else {
            $query->where('date', '>=', now()->startOfDay());
        }
        if ($to !== null && $to !== '') {
            $query->where('date', '<=', $to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('createur_id')) {
            $query->where('createur_id', $request->createur_id);
        }
        if ($request->filled('statut') && array_key_exists($request->statut, Planning::statutLabels())) {
            $query->where('statut', $request->statut);
        }

        // Les matchs d'aujourd'hui et demain sont affichés en haut ; la liste en dessous = les autres (pas de doublon)
        // Sur l'onglet Aujourd'hui : ne pas exclure aujourd'hui/demain pour qu'ils apparaissent dans la liste
        $today = now()->startOfDay()->format('Y-m-d');
        $demain = now()->addDay()->startOfDay()->format('Y-m-d');
        if ($vue !== self::VUE_AUJOURDHUI) {
            $query->where(function ($q) use ($today, $demain) {
                $q->where('date', '<', $today)->orWhere('date', '>', $demain);
            });
        }

        // Tri : ordre chronologique
        $matchs = $query->orderBy('date')->orderBy('heure')->orderBy('createur_id')->paginate(20)->withQueryString();

        $emailsCreateurs = User::where('role', User::ROLE_CREATEUR)->pluck('email');
        $createursEquipeId = $equipeAgenceId ?? ($user->isManageur() || $user->isSousManager() ? $user->equipe_id : null);
        $createurs = Createur::query()
            ->when($vue === self::VUE_UNIONS, fn ($q) => $q->unionsAgency())
            ->when($vue === self::VUE_PARTENAIRE, fn ($q) => $q->faucheuseAgency())
            ->when($vue === self::VUE_UNIONS && $createursEquipeId !== null, fn ($q) => $q->where('equipe_id', $createursEquipeId))
            ->when($vue === self::VUE_PARTENAIRE && $createursEquipeId !== null, fn ($q) => $q->where('equipe_id', '!=', $createursEquipeId))
            ->when($vue !== self::VUE_UNIONS && $vue !== self::VUE_PARTENAIRE && $equipeAgenceId !== null, fn ($q) => $q->where('equipe_id', $equipeAgenceId))
            ->when($vue !== self::VUE_UNIONS && $vue !== self::VUE_PARTENAIRE && $equipeAgenceId === null && ($user->isManageur() || $user->isSousManager()), fn ($q) => $q->where('equipe_id', $user->equipe_id))
            ->when($user->isAgent(), fn ($q) => $q->where('agent_id', $user->id))
            ->when($user->isCreateur(), fn ($q) => $q->where('email', $user->email))
            ->when(!$user->isCreateur() && !$user->isAgent(), fn ($q) => $q->where(fn ($q2) => $q2->whereNotNull('user_id')->orWhereIn('email', $emailsCreateurs)))
            ->orderBy('nom')
            ->get(['id', 'nom', 'pseudo_tiktok']);

        $equipes = $equipeAgenceId !== null
            ? Equipe::where('id', $equipeAgenceId)->get(['id', 'nom'])
            : Equipe::orderBy('nom')->get(['id', 'nom']);
        $showCatalogueAgence = ! $user->isCreateur() && $user->isFondateurPrincipal() && $equipeAgenceId === null;
        $showCatalogueEquipe = ($equipeAgenceId !== null || $user->isManageur() || $user->isSousManager() || $user->isAgent()) && ($equipeAgenceId ?? $user->equipe_id);
        $showCatalogueEquipeSelect = ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) && $equipeAgenceId === null && $equipes->isNotEmpty();

        // 3 onglets pour tous les rôles (y compris Créateur) : Aujourd'hui | Match Unions | Match partenaire
        $showTabsVue = true;
        $queryBase = array_filter([
            'from' => $from,
            'to' => $to,
            'type' => $request->get('type'),
            'createur_id' => $request->get('createur_id'),
            'statut' => $request->get('statut'),
            'equipe_id' => $request->get('equipe_id'),
        ], fn ($v) => $v !== null && $v !== '');
        $todayStr = now()->format('Y-m-d');
        $to7joursStr = now()->addDays(6)->format('Y-m-d');
        $urlAujourdhui = route('matches.index', array_merge($queryBase, ['vue' => self::VUE_AUJOURDHUI, 'from' => $todayStr, 'to' => $to7joursStr]));
        // Quand on est sur "Aujourd'hui", les liens Partenaire/Unions utilisent une période large pour éviter une liste vide
        $basePourPartenaireUnions = $vue === self::VUE_AUJOURDHUI
            ? array_merge($queryBase, [
                'from' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
                'to' => now()->addMonth()->endOfMonth()->format('Y-m-d'),
            ])
            : $queryBase;
        $urlPartenaire = route('matches.index', array_merge($basePourPartenaireUnions, ['vue' => self::VUE_PARTENAIRE]));
        $urlUnions = route('matches.index', array_merge($basePourPartenaireUnions, ['vue' => self::VUE_UNIONS]));

        $prochainsMatchs = collect();
        if ($user->isCreateur()) {
            if ($createurEquipeId !== null) {
                $prochainsMatchs = Planning::with(['createur', 'creePar', 'updatedPar'])
                    ->whereHas('createur', fn ($q) => $q->where('equipe_id', $createurEquipeId))
                    ->whereIn('type', array_keys(self::MATCH_TYPES))
                    ->where('date', '>=', now()->startOfDay())
                    ->whereIn('statut', [Planning::STATUT_PROGRAMME, Planning::STATUT_EN_COURS])
                    ->orderBy('date')
                    ->orderBy('heure')
                    ->limit(10)
                    ->get();
            } else {
                $createurIds = Createur::where('email', $user->email)->orWhere('user_id', $user->id)->pluck('id');
                $prochainsMatchs = Planning::with(['createur', 'creePar', 'updatedPar'])
                    ->whereIn('createur_id', $createurIds)
                    ->whereIn('type', array_keys(self::MATCH_TYPES))
                    ->where('date', '>=', now()->startOfDay())
                    ->whereIn('statut', [Planning::STATUT_PROGRAMME, Planning::STATUT_EN_COURS])
                    ->orderBy('date')
                    ->orderBy('heure')
                    ->limit(5)
                    ->get();
            }
        }
        // Matchs aujourd'hui et demain en haut — pour inviter les créateurs à se mélanger ; même périmètre que la liste
        $matchsAujourdhuiEtDemainQuery = Planning::with(['createur.equipe', 'creePar', 'updatedPar'])
            ->whereIn('type', array_keys(self::MATCH_TYPES))
            ->whereIn('date', [$today, $demain])
            ->orderBy('date')
            ->orderBy('heure')
            ->orderBy('createur_id');
        if ($user->isCreateur()) {
            if ($vue === self::VUE_AUJOURDHUI) {
                // Créateur sur Aujourd'hui : tous les matchs du jour
            } elseif ($vue === self::VUE_UNIONS) {
                // Catalogue Unions Agency (sans "Faucheuse")
                if ($createurEquipeId !== null) {
                    $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where('equipe_id', $createurEquipeId)->unionsAgency());
                } else {
                    $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where(function ($q2) use ($user) {
                        $q2->where('email', $user->email)->orWhere('user_id', $user->id);
                    })->unionsAgency());
                }
            } else {
                // Partenaire : uniquement les match off Faucheuse Agency
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->faucheuseAgency());
            }
        } elseif ($vue === self::VUE_AUJOURDHUI) {
            // Aujourd'hui = tous les matchs du jour (pas de filtre)
        } elseif ($vue === self::VUE_UNIONS) {
            $matchsAujourdhuiEtDemainQuery->whereDoesntHave('createur', fn ($q) => $q->faucheuseAgency());
            $unionsEquipeAjd = fn ($equipeId) => fn ($q) => $q->where('equipe_id', $equipeId)->unionsAgency();
            if ($user->isAgent()) {
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id)->unionsAgency());
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', $unionsEquipeAjd($user->equipe_id));
            } elseif ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
                if ($request->filled('equipe_id')) {
                    $matchsAujourdhuiEtDemainQuery->whereHas('createur', $unionsEquipeAjd($request->equipe_id));
                } else {
                    $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->unionsAgency());
                }
            } elseif ($equipeAgenceId !== null) {
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', $unionsEquipeAjd($equipeAgenceId));
            }
        } elseif ($vue === self::VUE_PARTENAIRE) {
            $partenaireEquipeAjd = fn ($equipeId) => fn ($q) => $q->where('equipe_id', $equipeId)->faucheuseAgency();
            if ($user->isAgent()) {
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id)->faucheuseAgency());
            } elseif ($equipeAgenceId !== null) {
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', $partenaireEquipeAjd($equipeAgenceId));
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', $partenaireEquipeAjd($user->equipe_id));
            } elseif ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
                if ($request->filled('equipe_id')) {
                    $matchsAujourdhuiEtDemainQuery->whereHas('createur', $partenaireEquipeAjd($request->equipe_id));
                }
            }
        } elseif ($user->isAgent()) {
            $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id));
        } elseif ($equipeAgenceId !== null) {
            $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where('equipe_id', $equipeAgenceId));
        } elseif ($user->isManageur() || $user->isSousManager()) {
            $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where('equipe_id', $user->equipe_id));
        } elseif ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
            if ($request->filled('equipe_id')) {
                $matchsAujourdhuiEtDemainQuery->whereHas('createur', fn ($q) => $q->where('equipe_id', $request->equipe_id));
            }
        }
        $matchsAujourdhuiEtDemain = $matchsAujourdhuiEtDemainQuery->get();

        $demandes = collect();
        if ($user->canProgrammerMatch()) {
            $demandes = DemandeMatch::with('createur')
                ->where('statut', DemandeMatch::STATUT_EN_ATTENTE)
                ->whereHas('createur', function ($q) use ($user, $equipeAgenceId) {
                    if ($user->isAgent()) {
                        $q->where('agent_id', $user->id);
                    } elseif ($equipeAgenceId !== null) {
                        $q->where('equipe_id', $equipeAgenceId);
                    } elseif ($user->isManageur() || $user->isSousManager()) {
                        $q->where('equipe_id', $user->equipe_id);
                    }
                })
                ->latest()
                ->get();
        }

        return view('matches.index', [
            'matchs' => $matchs,
            'createurs' => $createurs,
            'demandes' => $demandes,
            'prochainsMatchs' => $prochainsMatchs,
            'matchsAujourdhuiEtDemain' => $matchsAujourdhuiEtDemain,
            'equipes' => $equipes,
            'typeLabels' => self::MATCH_TYPES,
            'statutLabels' => Planning::statutLabels(),
            'from' => $from ?? now()->startOfMonth()->format('Y-m-d'),
            'to' => $to ?? now()->endOfMonth()->format('Y-m-d'),
            'filterType' => $request->get('type'),
            'filterCreateurId' => $request->get('createur_id'),
            'filterStatut' => $request->get('statut'),
            'vue' => $vue,
            'showCatalogueAgence' => $showCatalogueAgence,
            'showCatalogueEquipe' => $showCatalogueEquipe,
            'showCatalogueEquipeSelect' => $showCatalogueEquipeSelect,
            'showTabsVue' => $showTabsVue,
            'urlAujourdhui' => $urlAujourdhui,
            'urlPartenaire' => $urlPartenaire,
            'urlUnions' => $urlUnions,
            'filterEquipeId' => $request->get('equipe_id'),
        ]);
    }

    /**
     * Génère un PDF avec tous les matchs (filtres actuels de la page), logo Unions Agency.
     */
    public function pdf(Request $request)
    {
        $user = $request->user();
        $vue = $request->get('vue', $this->defaultVue($user));
        $query = Planning::with(['createur.equipe', 'creePar'])
            ->whereIn('type', array_keys(self::MATCH_TYPES));

        $equipeAgenceId = $user->scopeToAgenceEquipeId();
        if ($user->isCreateur()) {
            $query->whereHas('createur', fn ($q) => $q->where('email', $user->email));
        } elseif ($vue === self::VUE_UNIONS) {
            $query->whereDoesntHave('createur', fn ($q) => $q->faucheuseAgency());
            $unionsEquipePdf = fn ($equipeId) => fn ($q) => $q->where('equipe_id', $equipeId)->unionsAgency();
            if ($user->isManageur() || $user->isSousManager()) {
                $query->whereHas('createur', $unionsEquipePdf($user->equipe_id));
            } elseif ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
                if ($request->filled('equipe_id')) {
                    $query->whereHas('createur', $unionsEquipePdf($request->equipe_id));
                } else {
                    $query->whereHas('createur', fn ($q) => $q->unionsAgency());
                }
            } elseif ($equipeAgenceId !== null) {
                $query->whereHas('createur', $unionsEquipePdf($equipeAgenceId));
            } elseif ($user->isAgent()) {
                $query->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id)->unionsAgency());
            }
        } elseif ($vue === self::VUE_PARTENAIRE) {
            // Catalogue Faucheuse Agency (prénom/nom avec "Faucheuse")
            $partenaireEquipePdf = fn ($equipeId) => fn ($q) => $q->where('equipe_id', $equipeId)->faucheuseAgency();
            if ($user->isAgent()) {
                $query->whereHas('createur', fn ($q) => $q->where('agent_id', $user->id)->faucheuseAgency());
            } elseif ($equipeAgenceId !== null) {
                $query->whereHas('createur', $partenaireEquipePdf($equipeAgenceId));
            } elseif ($user->isManageur() || $user->isSousManager()) {
                $query->whereHas('createur', $partenaireEquipePdf($user->equipe_id));
            } elseif ($user->isFondateurPrincipal() || $user->isDirecteur() || $user->isSousDirecteur()) {
                if ($request->filled('equipe_id')) {
                    $query->whereHas('createur', $partenaireEquipePdf($request->equipe_id));
                } else {
                    $query->whereHas('createur', fn ($q) => $q->faucheuseAgency());
                }
            }
        }

        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('createur_id')) {
            $query->where('createur_id', $request->createur_id);
        }
        if ($request->filled('statut') && array_key_exists($request->statut, Planning::statutLabels())) {
            $query->where('statut', $request->statut);
        }

        $matchs = $query->latest('date')->orderBy('heure')->orderBy('createur_id')->get();
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

        $pdf = Pdf::loadView('matches.pdf', [
            'matchs' => $matchs,
            'typeLabels' => self::MATCH_TYPES,
            'statutLabels' => Planning::statutLabels(),
            'from' => $from,
            'to' => $to,
        ])->setPaper('a4', 'portrait');

        $filename = 'matchs-unions-agency-' . $from . '_' . $to . '.pdf';
        return $pdf->download($filename);
    }

    private function defaultVue(User $user): string
    {
        if ($user->isCreateur()) {
            $createur = Createur::with('equipe')->where('user_id', $user->id)->orWhere('email', $user->email)->first();
            if ($createur && $createur->equipe_id) {
                $equipe = $createur->equipe;
                if ($equipe && (stripos($equipe->nom, 'faucheuse') !== false || $equipe->est_partenaire)) {
                    return self::VUE_PARTENAIRE;
                }
            }
            return self::VUE_UNIONS;
        }
        return self::VUE_PARTENAIRE;
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if (! $user->canProgrammerMatch()) {
            abort(403, 'Seul votre agent peut programmer un match. Utilisez « Demander un match » pour lui transmettre votre demande.');
        }

        // 1) Toutes les fiches créateur existantes (table createurs)
        $fiches = Createur::with(['user', 'equipe', 'agent'])->orderBy('nom')->get();

        // 2) Utilisateurs qui sont agent ou fondateur (ou autre rôle) SANS fiche créateur : on crée la fiche à la volée pour qu'ils apparaissent dans la liste
        $usersSansFiche = User::with(['equipe'])
            ->whereIn('role', [
                User::ROLE_AGENT,
                User::ROLE_FONDATEUR,
                User::ROLE_MANAGEUR,
                User::ROLE_SOUS_MANAGER,
                User::ROLE_DIRECTEUR,
                User::ROLE_SOUS_DIRECTEUR,
                User::ROLE_CREATEUR,
            ])
            ->whereDoesntHave('createur')
            ->orderByRaw('LOWER(name) ASC')
            ->get();

        $fichesSupplementaires = $usersSansFiche->map(function ($u) {
            $fiche = $this->getOrCreateCreateurForUser($u);
            $fiche->load(['user', 'equipe', 'agent']);
            return $fiche;
        });

        $toutesFiches = $fiches->concat($fichesSupplementaires)->unique('id')->sortBy(fn ($f) => strtolower($f->user?->name ?? $f->nom ?? ''))->values();

        $createurs = $toutesFiches->map(function ($fiche) {
            $nom = $fiche->user?->name ?? $fiche->nom ?? '—';
            $pseudo = $fiche->user?->username ?? $fiche->pseudo_tiktok ?? '';
            return (object) [
                'id' => $fiche->id,
                'nom' => $nom,
                'pseudo_tiktok' => $pseudo,
                'equipe' => $fiche->equipe,
                'agent' => $fiche->agent,
            ];
        });

        return view('matches.create', [
            'createurs' => $createurs,
            'typeLabels' => self::MATCH_TYPES,
            'niveauLabels' => Planning::NIVEAUX_MATCH_OFF,
            'statutLabels' => Planning::statutLabels(),
            'defaultCreateurId' => $request->get('createur_id'),
            'defaultDate' => $request->get('date'),
            'defaultHeure' => $request->get('heure'),
            'defaultType' => $request->get('type'),
            'defaultNiveauMatch' => $request->get('niveau_match'),
            'defaultCreateurAdverse' => $request->get('createur_adverse_at') ?: $request->get('createur_adverse'),
            'defaultCreateurAdverseAgent' => $request->get('createur_adverse_agent'),
        ]);
    }

    /**
     * Lookup créateur adverse par @ TikTok (pour auto-complétion formulaire match).
     */
    public function lookupCreateurAdverse(Request $request)
    {
        if (! $request->user()->canProgrammerMatch()) {
            abort(403);
        }
        $at = CreateurAdverse::normalizeAt($request->get('at'));
        if ($at === '') {
            return response()->json(null, 404);
        }
        $adverse = CreateurAdverse::where('tiktok_at', $at)->first();
        if (! $adverse) {
            return response()->json(null, 404);
        }

        return response()->json([
            'nom' => $adverse->nom,
            'agence' => $adverse->agence ?? '',
            'agent' => $adverse->agent ?? '',
            'tiktok_at' => $adverse->tiktok_at,
            'telephone' => $adverse->telephone,
            'email' => $adverse->email,
            'autres_infos' => $adverse->autres_infos,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user->canProgrammerMatch()) {
            abort(403, 'Seul votre agent peut programmer un match.');
        }
        // Fondateurs (global + sous-agence), directeurs, manageurs, agents peuvent enregistrer un match

        $rules = [
            'createur_id' => 'required|exists:createurs,id',
            'date' => 'required|date',
            'heure' => 'nullable|string|max:5',
            'type' => 'required|in:'.implode(',', array_keys(self::MATCH_TYPES)),
            'niveau_match' => 'nullable|in:'.implode(',', array_keys(Planning::NIVEAUX_MATCH_OFF)),
            'avec_boost' => 'nullable|boolean',
            'statut' => 'nullable|in:'.implode(',', array_keys(Planning::statutLabels())),
            'raison' => 'nullable|string|max:255',
            'createur_adverse' => 'required|string|max:255',
            'createur_adverse_agence' => 'nullable|string|max:255',
            'createur_adverse_agent' => 'nullable|string|max:255',
            'createur_adverse_at' => 'required|string|max:100',
            'createur_adverse_numero' => 'required|string|max:50',
            'createur_adverse_email' => 'nullable|string|max:255',
            'createur_adverse_autres' => 'nullable|string|max:2000',
        ];
        if ($request->type === 'match_off') {
            $rules['niveau_match'] = 'required|in:'.implode(',', array_keys(Planning::NIVEAUX_MATCH_OFF));
        }
        $request->validate($rules);

        // Fondateur sous-agence : uniquement les créateurs de sa team
        if ($user->estFondateurSousAgence() && $user->equipe_id) {
            $createur = Createur::find($request->createur_id);
            if (! $createur || $createur->equipe_id != $user->equipe_id) {
                return back()->with('error', 'Vous ne pouvez programmer un match que pour un créateur de votre agence.');
            }
        }

        $exists = Planning::where('createur_id', $request->createur_id)
            ->where('date', $request->date)
            ->where('type', $request->type)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Un match existe déjà pour ce créateur à cette date.');
        }

        $atNormalized = CreateurAdverse::normalizeAt($request->createur_adverse_at);
        CreateurAdverse::updateOrCreate(
            ['tiktok_at' => $atNormalized],
            [
                'nom' => $request->createur_adverse,
                'agence' => $request->createur_adverse_agence ?: null,
                'agent' => $request->createur_adverse_agent ?: null,
                'telephone' => $request->createur_adverse_numero,
                'email' => $request->createur_adverse_email ?: null,
                'autres_infos' => $request->createur_adverse_autres ?: null,
            ]
        );

        $planning = Planning::create([
            'createur_id' => $request->createur_id,
            'date' => $request->date,
            'heure' => $request->filled('heure') ? $request->heure : null,
            'type' => $request->type,
            'niveau_match' => $request->type === 'match_off' ? $request->niveau_match : null,
            'avec_boost' => $request->boolean('avec_boost'),
            'statut' => $request->filled('statut') ? $request->statut : Planning::STATUT_PROGRAMME,
            'raison' => $request->raison,
            'createur_adverse' => $request->createur_adverse,
            'createur_adverse_agence' => $request->createur_adverse_agence ?: null,
            'createur_adverse_agent' => $request->createur_adverse_agent ?: null,
            'createur_adverse_at' => $atNormalized,
            'createur_adverse_numero' => $request->createur_adverse_numero,
            'createur_adverse_email' => $request->createur_adverse_email ?: null,
            'createur_adverse_autres' => $request->createur_adverse_autres ?: null,
            'cree_par' => $request->user()->id,
        ]);

        $createur = Createur::find($request->createur_id);
        if ($createur) {
            $userToNotify = $createur->user_id ? $createur->user : User::where('email', $createur->email)->where('role', User::ROLE_CREATEUR)->first();
            if ($userToNotify) {
                $userToNotify->notify(new MatchProgrammeNotification($planning));
            }
        }

        DemandeMatch::where('createur_id', $request->createur_id)
            ->where('date_souhaitee', $request->date)
            ->where('type', $request->type)
            ->where('statut', DemandeMatch::STATUT_EN_ATTENTE)
            ->update(['statut' => DemandeMatch::STATUT_PROGRAMMEE]);

        return redirect()->route('matches.index')->with('success', 'Match programmé.');
    }

    public function edit(Request $request, Planning $planning)
    {
        if ($request->user()->isCreateur()) {
            abort(403, 'Seul votre agent peut modifier un match. Faites-lui une demande si besoin.');
        }
        if (! in_array($planning->type, array_keys(self::MATCH_TYPES))) {
            abort(404);
        }
        $this->authorizeMatch($request->user(), $planning);

        $user = $request->user();

        // Même liste que create : toutes les fiches createurs + agents/fondateurs/manageurs sans fiche (fiche créée à la volée)
        $fiches = Createur::with(['user', 'equipe', 'agent'])->orderBy('nom')->get();
        $usersSansFiche = User::with(['equipe'])
            ->whereIn('role', [
                User::ROLE_AGENT,
                User::ROLE_FONDATEUR,
                User::ROLE_MANAGEUR,
                User::ROLE_SOUS_MANAGER,
                User::ROLE_DIRECTEUR,
                User::ROLE_SOUS_DIRECTEUR,
                User::ROLE_CREATEUR,
            ])
            ->whereDoesntHave('createur')
            ->orderByRaw('LOWER(name) ASC')
            ->get();
        $fichesSupplementaires = $usersSansFiche->map(function ($u) {
            $fiche = $this->getOrCreateCreateurForUser($u);
            $fiche->load(['user', 'equipe', 'agent']);
            return $fiche;
        });
        $toutesFiches = $fiches->concat($fichesSupplementaires)->unique('id')->sortBy(fn ($f) => strtolower($f->user?->name ?? $f->nom ?? ''))->values();
        $createurs = $toutesFiches->map(function ($fiche) {
            $nom = $fiche->user?->name ?? $fiche->nom ?? '—';
            $pseudo = $fiche->user?->username ?? $fiche->pseudo_tiktok ?? '';
            return (object) [
                'id' => $fiche->id,
                'nom' => $nom,
                'pseudo_tiktok' => $pseudo,
                'equipe' => $fiche->equipe,
                'agent' => $fiche->agent,
            ];
        });

        return view('matches.edit', [
            'match' => $planning,
            'createurs' => $createurs,
            'typeLabels' => self::MATCH_TYPES,
            'niveauLabels' => Planning::NIVEAUX_MATCH_OFF,
            'statutLabels' => Planning::statutLabels(),
        ]);
    }

    public function update(Request $request, Planning $planning)
    {
        if ($request->user()->isCreateur()) {
            abort(403, 'Seul votre agent peut modifier un match.');
        }
        if (! in_array($planning->type, array_keys(self::MATCH_TYPES))) {
            abort(404);
        }
        $this->authorizeMatch($request->user(), $planning);

        $rules = [
            'createur_id' => 'required|exists:createurs,id',
            'date' => 'required|date',
            'heure' => 'nullable|string|max:5',
            'type' => 'required|in:'.implode(',', array_keys(self::MATCH_TYPES)),
            'niveau_match' => 'nullable|in:'.implode(',', array_keys(Planning::NIVEAUX_MATCH_OFF)),
            'avec_boost' => 'nullable|boolean',
            'statut' => 'nullable|in:'.implode(',', array_keys(Planning::statutLabels())),
            'raison' => 'nullable|string|max:255',
            'createur_adverse' => 'required|string|max:255',
            'createur_adverse_agence' => 'nullable|string|max:255',
            'createur_adverse_agent' => 'nullable|string|max:255',
            'createur_adverse_at' => 'required|string|max:100',
            'createur_adverse_numero' => 'required|string|max:50',
            'createur_adverse_email' => 'nullable|string|max:255',
            'createur_adverse_autres' => 'nullable|string|max:2000',
        ];
        if ($request->type === 'match_off') {
            $rules['niveau_match'] = 'required|in:'.implode(',', array_keys(Planning::NIVEAUX_MATCH_OFF));
        }
        $request->validate($rules);

        $user = $request->user();
        if ($user->estFondateurSousAgence() && $user->equipe_id) {
            $createur = Createur::find($request->createur_id);
            if (! $createur || $createur->equipe_id != $user->equipe_id) {
                return back()->with('error', 'Vous ne pouvez programmer un match que pour un créateur de votre agence.');
            }
        }

        $exists = Planning::where('createur_id', $request->createur_id)
            ->where('date', $request->date)
            ->where('type', $request->type)
            ->where('id', '!=', $planning->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Un match existe déjà pour ce créateur à cette date.');
        }

        $atNormalized = CreateurAdverse::normalizeAt($request->createur_adverse_at);
        CreateurAdverse::updateOrCreate(
            ['tiktok_at' => $atNormalized],
            [
                'nom' => $request->createur_adverse,
                'agence' => $request->createur_adverse_agence ?: null,
                'agent' => $request->createur_adverse_agent ?: null,
                'telephone' => $request->createur_adverse_numero,
                'email' => $request->createur_adverse_email ?: null,
                'autres_infos' => $request->createur_adverse_autres ?: null,
            ]
        );

        $nouveauStatut = $request->filled('statut') ? $request->statut : Planning::STATUT_PROGRAMME;
        $planning->update([
            'createur_id' => $request->createur_id,
            'date' => $request->date,
            'heure' => $request->filled('heure') ? $request->heure : null,
            'type' => $request->type,
            'niveau_match' => $request->type === 'match_off' ? $request->niveau_match : null,
            'avec_boost' => $request->boolean('avec_boost'),
            'statut' => $nouveauStatut,
            'raison' => $request->raison,
            'createur_adverse' => $request->createur_adverse,
            'createur_adverse_agence' => $request->createur_adverse_agence ?: null,
            'createur_adverse_agent' => $request->createur_adverse_agent ?: null,
            'createur_adverse_at' => $atNormalized,
            'createur_adverse_numero' => $request->createur_adverse_numero,
            'createur_adverse_email' => $request->createur_adverse_email ?: null,
            'createur_adverse_autres' => $request->createur_adverse_autres ?: null,
            'updated_par' => $request->user()->id,
        ]);

        if ($nouveauStatut === Planning::STATUT_ACCEPTEE) {
            ScoreFidelite::addPointsForMatch($planning);
        }

        return redirect()->route('matches.index')->with('success', 'Match mis à jour.');
    }

    public function destroy(Planning $planning)
    {
        if (auth()->user()->isCreateur()) {
            abort(403, 'Seul votre agent peut supprimer un match.');
        }
        if (! in_array($planning->type, array_keys(self::MATCH_TYPES))) {
            abort(404);
        }
        $this->authorizeMatch(auth()->user(), $planning);
        $planning->delete();
        return back()->with('success', 'Match supprimé.');
    }

    public function demandeCreate(Request $request)
    {
        if (! $request->user()->isCreateur()) {
            abort(403);
        }
        $createur = $this->getOrCreateCreateurForUser($request->user());
        return view('matches.demande', [
            'createur' => $createur,
            'typeLabels' => self::MATCH_TYPES,
        ]);
    }

    public function demandeStore(Request $request)
    {
        if (! $request->user()->isCreateur()) {
            abort(403);
        }
        $createur = $this->getOrCreateCreateurForUser($request->user());
        $request->validate([
            'date_souhaitee' => 'required|date',
            'heure_souhaitee' => 'nullable|string|max:5',
            'type' => 'required|in:'.implode(',', array_keys(self::MATCH_TYPES)),
            'qui_en_face' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:500',
        ]);
        $quiEnFace = $request->filled('qui_en_face') ? trim($request->qui_en_face) : null;
        $demande = DemandeMatch::create([
            'createur_id' => $createur->id,
            'date_souhaitee' => $request->date_souhaitee,
            'heure_souhaitee' => $request->filled('heure_souhaitee') ? substr($request->heure_souhaitee, 0, 5) : null,
            'type' => $request->type,
            'qui_en_face' => $quiEnFace,
            'message' => $request->message ?: null,
            'statut' => DemandeMatch::STATUT_EN_ATTENTE,
        ]);

        $createur->load('agent', 'equipe');
        $toNotify = collect();
        if ($createur->agent_id && $createur->agent) {
            $toNotify->push($createur->agent);
        }
        if ($createur->equipe_id && $createur->equipe) {
            $manageurs = User::whereIn('role', [User::ROLE_MANAGEUR, User::ROLE_SOUS_MANAGER])
                ->where('equipe_id', $createur->equipe_id)
                ->get();
            foreach ($manageurs as $m) {
                if (! $toNotify->contains('id', $m->id)) {
                    $toNotify->push($m);
                }
            }
        }
        foreach ($toNotify as $user) {
            $user->notify(new DemandeMatchNotification($demande));
        }

        return redirect()->route('matches.index')->with('success', 'Votre demande a été envoyée à votre agent. Il pourra programmer le match après validation.');
    }

    /** Refuser une demande de match (agent / manageur / sous-manager). */
    public function demandeRefuse(Request $request, DemandeMatch $demande)
    {
        $user = $request->user();
        if (! $user->canProgrammerMatch()) {
            abort(403, 'Seuls les agents et au-dessus peuvent refuser une demande.');
        }
        $demande->load('createur');
        if ($user->isAgent() && $demande->createur->agent_id !== $user->id) {
            abort(403, 'Vous ne pouvez refuser que les demandes de vos créateurs.');
        }
        if (($user->isManageur() || $user->isSousManager()) && $demande->createur->equipe_id !== $user->equipe_id) {
            abort(403, 'Vous ne pouvez refuser que les demandes de votre équipe.');
        }
        if ($demande->statut !== DemandeMatch::STATUT_EN_ATTENTE) {
            return redirect()->route('matches.index')->with('error', 'Cette demande a déjà été traitée.');
        }
        $demande->update(['statut' => DemandeMatch::STATUT_REFUSEE]);

        return redirect()->route('matches.index')->with('success', 'Demande refusée.');
    }

    /** Crée une fiche créateur à partir du User si elle n'existe pas (pour demandes de match, liste agents/fondateurs, etc.). */
    private function getOrCreateCreateurForUser(User $user): Createur
    {
        $createur = Createur::where('user_id', $user->id)->first();
        if ($createur) {
            return $createur;
        }
        if (!empty($user->email)) {
            $createur = Createur::where('email', $user->email)->first();
            if ($createur) {
                $createur->update(['user_id' => $user->id]);
                return $createur;
            }
        }
        $nom = trim($user->name) ?: 'Utilisateur';
        return Createur::create([
            'nom' => $nom,
            'email' => !empty($user->email) ? $user->email : null,
            'user_id' => $user->id,
        ]);
    }

    private function authorizeMatch($user, Planning $planning): void
    {
        $planning->load('createur');
        if ($user->estFondateurSousAgence() && $user->equipe_id && $planning->createur->equipe_id != $user->equipe_id) {
            abort(403, 'Ce match ne fait pas partie de votre agence.');
        }
        if (($user->isManageur() || $user->isSousManager()) && $planning->createur->equipe_id !== $user->equipe_id) {
            abort(403);
        }
        if ($user->isAgent() && $planning->createur->agent_id !== $user->id) {
            abort(403);
        }
        if ($user->isCreateur() && $planning->createur->email !== $user->email) {
            abort(403);
        }
    }
}
