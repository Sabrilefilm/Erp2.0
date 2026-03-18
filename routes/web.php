<?php

use App\Http\Controllers\AideController;
use App\Http\Controllers\DocumentsOfficielsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeRequiredController;
use App\Http\Controllers\Auth\PasswordResetCodeController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\CreateurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\DonneesMatchController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\FormationQuizController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\MessagerieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushAdminController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RecompenseController;
use App\Http\Controllers\RapportVendrediController;
use App\Http\Controllers\RegleController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\ScoreFideliteController;
use App\Http\Controllers\ScoreIntegriteController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

// Authentification (invités)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Compte temporairement bloqué (trop de tentatives de connexion)
Route::get('/compte-bloque-temporaire', function () {
    return view('auth.compte-bloque-temporaire');
})->name('compte-bloque-temporaire');

// Réinitialisation par code (identifiant + code fourni par l'admin)
Route::get('/reinitialiser-avec-code', [PasswordResetCodeController::class, 'showResetWithCodeForm'])
    ->middleware('guest')->name('password.reset-with-code.form');
Route::post('/reinitialiser-avec-code', [PasswordResetCodeController::class, 'resetWithCode'])
    ->middleware('guest')->name('password.reset-with-code');

// Mot de passe oublié (par e-mail — optionnel)
Route::get('/mot-de-passe-oublie', function () {
    return view('auth.passwords.email');
})->middleware('guest')->name('password.request');

Route::post('/mot-de-passe-oublie', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reinitialiser-mot-de-passe/{token}', function (string $token) {
    return view('auth.passwords.reset', [
        'token' => $token,
        'email' => request('email'),
    ]);
})->middleware('guest')->name('password.reset');

Route::post('/reinitialiser-mot-de-passe', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// Pages légales (accessibles sans connexion — affichées sur la page de connexion)
Route::get('/contrat', fn () => view('legal.page', ['title' => 'Contrat de prestation', 'slug' => 'contrat']))->name('legal.contrat');
Route::get('/reglement', fn () => view('legal.page', ['title' => 'Règlement intérieur', 'slug' => 'reglement']))->name('legal.reglement');
Route::get('/rgpd', fn () => view('legal.page', ['title' => 'RGPD', 'slug' => 'rgpd']))->name('legal.rgpd');
Route::get('/politique-confidentialite', fn () => view('legal.page', ['title' => 'Politique de Confidentialité', 'slug' => 'politique-confidentialite']))->name('legal.confidentialite');
Route::get('/mentions-legales', fn () => view('legal.page', ['title' => 'Mentions Légales', 'slug' => 'mentions-legales']))->name('legal.mentions');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Routes protégées (authentifiées)
Route::middleware(['auth', 'compte.non.bloque', 'must.change.password', 'contrat.reglement.accepted'])->group(function () {
    Route::get('/compte-bloque', function () {
        if (! auth()->user() || ! auth()->user()->compte_bloque) {
            return redirect()->route('dashboard');
        }
        return view('auth.compte-bloque');
    })->name('compte-bloque');

    Route::get('/password/change-required', [PasswordChangeRequiredController::class, 'show'])->name('password.change-required');
    Route::post('/password/change-required', [PasswordChangeRequiredController::class, 'store'])->name('password.change-required.store');

    Route::get('/', [DashboardController::class, '__invoke'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, '__invoke'])->name('dashboard.home');
    Route::get('/aide', AideController::class)->name('aide.index');
    Route::get('/documents-officiels', [DocumentsOfficielsController::class, 'index'])->name('documents-officiels.index');
    Route::get('/documents-officiels/reglement', [DocumentsOfficielsController::class, 'reglement'])->name('documents-officiels.reglement');
    Route::post('/documents-officiels/update-info', [DocumentsOfficielsController::class, 'updateInfo'])->name('documents-officiels.update-info');
    Route::post('/documents-officiels/sign', [DocumentsOfficielsController::class, 'sign'])->name('documents-officiels.sign');
    Route::post('/documents-officiels/accept-reglement', [DocumentsOfficielsController::class, 'acceptReglement'])->name('documents-officiels.accept-reglement');
    Route::post('/documents-officiels/accept-tout', [DocumentsOfficielsController::class, 'acceptTout'])->name('documents-officiels.accept-tout');
    Route::get('/score-integrite', [ScoreIntegriteController::class, 'index'])->name('score-integrite.index');
    Route::get('/score-fidelite', [ScoreFideliteController::class, 'index'])->name('score-fidelite.index');
    Route::post('/score-fidelite/update-score', [ScoreFideliteController::class, 'updateScore'])->name('score-fidelite.update-score');

    // Utilisateurs
    Route::get('/users/table', [UserController::class, 'tableFragment'])->name('users.table');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Générer un code de réinitialisation (manageur et au-dessus)
    Route::get('/generer-code-mot-de-passe', [PasswordResetCodeController::class, 'showGenerateForm'])->name('password.generate-code.form');
    Route::post('/generer-code-mot-de-passe', [PasswordResetCodeController::class, 'generate'])->name('password.generate-code');

    // Créateurs
    Route::get('/createurs', [CreateurController::class, 'index'])->name('createurs.index');
    Route::get('/mes-createurs', [CreateurController::class, 'mesCreateurs'])->name('createurs.mes-createurs');
    Route::get('/createurs/{createur}/contrat-pdf', [CreateurController::class, 'contratPdf'])->name('createurs.contrat-pdf');
    Route::get('/createurs/{createur}', [CreateurController::class, 'show'])->name('createurs.show');
    Route::put('/createurs/{createur}/notes', [CreateurController::class, 'updateNotes'])->name('createurs.update-notes');
    Route::put('/createurs/{createur}/attribution', [CreateurController::class, 'updateAttribution'])->name('createurs.update-attribution');
    Route::post('/createurs/{createur}/commentaires', [CreateurController::class, 'storeCommentaire'])->name('createurs.commentaires.store');
    Route::delete('/createurs/{createur}', [CreateurController::class, 'destroy'])->name('createurs.destroy');

    // Planning : redirigé vers Matchs (tout se gère dans Matchs)
    Route::get('/planning', fn () => redirect()->route('matches.index'))->name('planning.index');

    // Matchs (V2)
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/pdf', [MatchController::class, 'pdf'])->name('matches.pdf');
    Route::get('/matches/create', [MatchController::class, 'create'])->name('matches.create');
    Route::get('/matches/createur-adverse-lookup', [MatchController::class, 'lookupCreateurAdverse'])->name('matches.createur-adverse.lookup');
    Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
    Route::get('/matches/demande', [MatchController::class, 'demandeCreate'])->name('matches.demande.create');
    Route::post('/matches/demande', [MatchController::class, 'demandeStore'])->name('matches.demande.store');
    Route::post('/matches/demande/{demande}/refuser', [MatchController::class, 'demandeRefuse'])->name('matches.demande.refuse');
    Route::get('/matches/{planning}/edit', [MatchController::class, 'edit'])->name('matches.edit');
    Route::put('/matches/{planning}', [MatchController::class, 'update'])->name('matches.update');
    Route::delete('/matches/{planning}', [MatchController::class, 'destroy'])->name('matches.destroy');

    // Données match — répertoire créateurs adverses (fondateur global uniquement)
    Route::get('/donnees-match', [DonneesMatchController::class, 'index'])->name('donnees-match.index');
    Route::post('/donnees-match', [DonneesMatchController::class, 'store'])->name('donnees-match.store');
    Route::get('/donnees-match/{adverse}/edit', [DonneesMatchController::class, 'edit'])->name('donnees-match.edit');
    Route::put('/donnees-match/{adverse}', [DonneesMatchController::class, 'update'])->name('donnees-match.update');
    Route::delete('/donnees-match/{adverse}', [DonneesMatchController::class, 'destroy'])->name('donnees-match.destroy');

    // Messagerie (V1)
    Route::get('/messagerie', [MessagerieController::class, 'index'])->name('messagerie.index');
    Route::get('/messagerie/message/{message}/fichier', [MessagerieController::class, 'fichier'])->name('messagerie.fichier');
    Route::get('/messagerie/{user}', [MessagerieController::class, 'conversation'])->name('messagerie.conversation');
    Route::post('/messagerie/{user}/send', [MessagerieController::class, 'send'])->name('messagerie.send');
    Route::post('/messagerie/{user}/read', [MessagerieController::class, 'markRead'])->name('messagerie.read');
    Route::delete('/messagerie/{user}', [MessagerieController::class, 'deleteConversation'])->name('messagerie.delete');
    Route::get('/messagerie-groupe', [MessagerieController::class, 'groupeForm'])->name('messagerie.groupe');
    Route::post('/messagerie-groupe', [MessagerieController::class, 'groupeSend'])->name('messagerie.groupe.send');

    // Règles
    Route::get('/regles', [RegleController::class, 'index'])->name('regles.index');
    Route::get('/regles/create', [RegleController::class, 'create'])->name('regles.create');
    Route::post('/regles', [RegleController::class, 'store'])->name('regles.store');
    Route::get('/regles/{regle}/edit', [RegleController::class, 'edit'])->name('regles.edit');
    Route::put('/regles/{regle}', [RegleController::class, 'update'])->name('regles.update');
    Route::delete('/regles/{regle}', [RegleController::class, 'destroy'])->name('regles.destroy');

    // Rapport du mois (obligatoire pour tous sauf fondateurs et créateurs ; consultation par fondateurs)
    Route::get('/rapport-vendredi', [RapportVendrediController::class, 'index'])->name('rapport-vendredi.index');
    Route::post('/rapport-vendredi', [RapportVendrediController::class, 'store'])->name('rapport-vendredi.store');
    Route::post('/rapport-vendredi/{rapport}/valider', [RapportVendrediController::class, 'valider'])->name('rapport-vendredi.valider');

    // Formations et contenus — une seule page : le catalogue (style V1)
    Route::get('/formations', [FormationController::class, 'index'])->name('formations.index');
    Route::get('/formations/catalogue', fn () => redirect()->route('formations.index'))->name('formations.catalogue');
    Route::get('/formations/create', [FormationController::class, 'create'])->name('formations.create');
    Route::post('/formations/catalogues', [FormationController::class, 'storeCatalogue'])->name('formations.catalogues.store');
    Route::delete('/formations/catalogues/{catalogue}', [FormationController::class, 'destroyCatalogue'])->name('formations.catalogues.destroy');
    Route::get('/formations/{formation}', [FormationController::class, 'show'])->name('formations.show');
    Route::get('/formations/{formation}/contenu', [FormationController::class, 'contenu'])->name('formations.contenu');
    Route::get('/formations/{formation}/media', [FormationController::class, 'media'])->name('formations.media');
    Route::get('/formations/{formation}/fichier', [FormationController::class, 'fichier'])->name('formations.fichier');
    Route::get('/formations/{formation}/quiz', [FormationQuizController::class, 'show'])->name('formations.quiz.show');
    Route::post('/formations/{formation}/quiz', [FormationQuizController::class, 'submit'])->name('formations.quiz.submit');
    Route::get('/formations/{formation}/quiz/result', [FormationQuizController::class, 'result'])->name('formations.quiz.result');
    Route::post('/formations/{formation}/quiz/generate', [FormationQuizController::class, 'generate'])->name('formations.quiz.generate');
    Route::post('/formations', [FormationController::class, 'store'])->name('formations.store');
    Route::get('/formations/{formation}/edit', [FormationController::class, 'edit'])->name('formations.edit');
    Route::put('/formations/{formation}', [FormationController::class, 'update'])->name('formations.update');
    Route::delete('/formations/{formation}', [FormationController::class, 'destroy'])->name('formations.destroy');

    // Notifications (V1)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // Push (Web Push) — abonnement pour notifications sur téléphone / desktop
    Route::get('/push-public-key', [PushSubscriptionController::class, 'publicKey'])->name('push.public-key');
    Route::post('/push-subscription', [PushSubscriptionController::class, 'store'])->name('push.subscription.store');
    Route::delete('/push-subscription', [PushSubscriptionController::class, 'destroy'])->name('push.subscription.destroy');

    // Récompenses
    Route::get('/recompenses', [RecompenseController::class, 'index'])->name('recompenses.index');
    Route::get('/recompenses/iban-banque', [RecompenseController::class, 'ibanBanque'])->name('recompenses.iban-banque');
    Route::get('/recompenses/{recompense}', [RecompenseController::class, 'show'])->name('recompenses.show');
    Route::post('/recompenses', [RecompenseController::class, 'store'])->name('recompenses.store');
    Route::post('/recompenses/{recompense}/choisir-type', [RecompenseController::class, 'choisirType'])->name('recompenses.choisir-type');
    Route::post('/recompenses/{recompense}/refuser', [RecompenseController::class, 'refuser'])->name('recompenses.refuser');
    Route::put('/recompenses/{recompense}', [RecompenseController::class, 'update'])->name('recompenses.update');
    Route::get('/recompenses/{recompense}/facture', [RecompenseController::class, 'facturePdf'])->name('recompenses.facture');
    Route::delete('/recompenses/{recompense}', [RecompenseController::class, 'destroy'])->name('recompenses.destroy');

    // Blacklist
    Route::get('/blacklist', [BlacklistController::class, 'index'])->name('blacklist.index');
    Route::post('/blacklist', [BlacklistController::class, 'store'])->name('blacklist.store');
    Route::delete('/blacklist/{blacklist}', [BlacklistController::class, 'destroy'])->name('blacklist.destroy');

    // Agences (équipes) — Fondateur Global uniquement (middleware fondateur.only)
    Route::middleware('fondateur.only')->group(function () {
        Route::get('equipes/attribution', [EquipeController::class, 'attribution'])->name('equipes.attribution');
        Route::put('equipes/attribution/{user}', [EquipeController::class, 'assignerAgenceFromAttribution'])->name('equipes.attribution.assign');
        Route::get('equipes/{equipe}/membres', [EquipeController::class, 'membres'])->name('equipes.membres');
        Route::post('equipes/{equipe}/attribuer', [EquipeController::class, 'attribuer'])->name('equipes.attribuer');
        Route::put('equipes/{equipe}/membres/{user}/changer-agence', [EquipeController::class, 'changerAgence'])->name('equipes.changer-agence');
        Route::resource('equipes', EquipeController::class);
    });

    // Import Excel + Diagnostic + Gestion infractions / score (Fondateur uniquement)
    Route::middleware('fondateur.only')->group(function () {
        Route::get('/import', [ImportExcelController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportExcelController::class, 'store'])->name('import.store');
        Route::get('/import/template', [ImportExcelController::class, 'template'])->name('import.template');
        Route::get('/import/export-donnees', [ImportExcelController::class, 'exportDonnees'])->name('import.export-donnees');
        Route::get('/import/corriger-heures-jours', [ImportExcelController::class, 'corrigerHeuresJours'])->name('import.corriger-heures-jours');
        Route::post('/import/corriger-heures-jours', [ImportExcelController::class, 'mettreAJourHeuresJours'])->name('import.mettre-a-jour-heures-jours');
        Route::delete('/import/logs/{import_log}', [ImportExcelController::class, 'destroyLog'])->name('import.logs.destroy');
        Route::get('/diagnostic', [DiagnosticController::class, 'index'])->name('diagnostic.index');
        Route::post('/diagnostic/maintenance/activate', [DiagnosticController::class, 'activateMaintenance'])->name('diagnostic.maintenance.activate');
        Route::post('/diagnostic/maintenance/deactivate', [DiagnosticController::class, 'deactivateMaintenance'])->name('diagnostic.maintenance.deactivate');
        Route::get('/score-integrite/gestion', [ScoreIntegriteController::class, 'gestion'])->name('score-integrite.gestion');
        Route::post('/score-integrite/gestion', [ScoreIntegriteController::class, 'storeInfraction'])->name('score-integrite.store-infraction');
        // Notifications push (admin)
        Route::get('/push-admin', [PushAdminController::class, 'index'])->name('push-admin.index');
        Route::get('/push-admin/send', [PushAdminController::class, 'sendForm'])->name('push-admin.send');
        Route::post('/push-admin/send', [PushAdminController::class, 'send'])->name('push-admin.send.store');
        Route::get('/push-admin/templates', [PushAdminController::class, 'templates'])->name('push-admin.templates');
        Route::post('/push-admin/templates', [PushAdminController::class, 'storeTemplate'])->name('push-admin.templates.store');
        Route::put('/push-admin/templates/{template}', [PushAdminController::class, 'updateTemplate'])->name('push-admin.templates.update');
        Route::get('/push-admin/scheduled', [PushAdminController::class, 'scheduledForm'])->name('push-admin.scheduled');
        Route::post('/push-admin/scheduled', [PushAdminController::class, 'storeScheduled'])->name('push-admin.scheduled.store');
        Route::get('/push-admin/history', [PushAdminController::class, 'history'])->name('push-admin.history');
    });
});
