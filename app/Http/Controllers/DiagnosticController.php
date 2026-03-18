<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class DiagnosticController extends Controller
{
    /**
     * Page diagnostic : tout en un — IP, connexion actuelle, serveur, base de données, maintenance.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // --- Réseau / Connexion (côté visiteur) ---
        $clientIp = $request->ip();
        $userAgent = $request->userAgent();
        $url = $request->fullUrl();
        $method = $request->method();
        $forwardedFor = $request->header('X-Forwarded-For');
        $realIp = $request->header('X-Real-IP');

        // --- Connexion actuelle (session / utilisateur) ---
        $sessionDriver = config('session.driver');
        $sessionId = Session::getId();

        // --- Serveur ---
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $env = config('app.env');
        $debug = config('app.debug');
        $timezone = config('app.timezone');
        $locale = config('app.locale');
        $appUrl = config('app.url');

        $dbDriver = config('database.default');
        $dbName = null;
        $dbStatus = 'inconnu';
        $dbError = null;
        try {
            DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();
            $dbStatus = 'OK';
        } catch (\Throwable $e) {
            $dbStatus = 'erreur';
            $dbError = $e->getMessage();
        }

        return view('diagnostic.index', [
            'clientIp' => $clientIp,
            'userAgent' => $userAgent,
            'url' => $url,
            'method' => $method,
            'forwardedFor' => $forwardedFor,
            'realIp' => $realIp,
            'user' => $user,
            'sessionDriver' => $sessionDriver,
            'sessionId' => $sessionId,
            'phpVersion' => $phpVersion,
            'laravelVersion' => $laravelVersion,
            'env' => $env,
            'debug' => $debug,
            'timezone' => $timezone,
            'locale' => $locale,
            'appUrl' => $appUrl,
            'dbDriver' => $dbDriver,
            'dbName' => $dbName,
            'dbStatus' => $dbStatus,
            'dbError' => $dbError,
            'maintenanceEnabled' => CheckMaintenance::isEnabled(),
        ]);
    }

    /**
     * Active le mode maintenance : seul le Fondateur principal pourra se connecter.
     */
    public function activateMaintenance(Request $request)
    {
        $path = storage_path('app/maintenance_mode');
        File::put($path, (string) now()->toIso8601String());

        return redirect()->route('diagnostic.index')->with('success', 'Le service est maintenant en maintenance. Seul le Fondateur principal peut se connecter.');
    }

    /**
     * Désactive le mode maintenance.
     */
    public function deactivateMaintenance(Request $request)
    {
        $path = storage_path('app/maintenance_mode');
        if (File::exists($path)) {
            File::delete($path);
        }

        return redirect()->route('diagnostic.index')->with('success', 'La maintenance a été désactivée. Le site est à nouveau accessible à tous.');
    }
}
