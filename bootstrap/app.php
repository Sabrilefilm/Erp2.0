<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'fondateur.only' => \App\Http\Middleware\EnsureFondateurOnly::class,
            'compte.non.bloque' => \App\Http\Middleware\EnsureCompteNonBloque::class,
            'must.change.password' => \App\Http\Middleware\EnsureMustChangePassword::class,
            'contrat.reglement.accepted' => \App\Http\Middleware\EnsureContratAndReglementAccepted::class,
        ]);
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\CheckMaintenance::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('matchs:send-rappels')->everyMinute();
        $schedule->command('matchs:supprimer-passes')->everyMinute();
        $schedule->command('push:process-scheduled')->everyFiveMinutes();
        // Le 1er de chaque mois à 00:05 : archive le mois précédent puis remet à zéro jours/heures/diamants des créateurs
        $schedule->command('createurs:reset-stats-mois')->monthlyOn(1, '00:05');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Session expirée. Veuillez rafraîchir la page.'], 419);
            }
            return redirect()->guest(route('login'))
                ->with('error', 'Session expirée. Veuillez vous reconnecter.');
        });
    })->create();
