<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    protected function maintenanceFilePath(): string
    {
        return storage_path('app/maintenance_mode');
    }

    public static function isEnabled(): bool
    {
        return File::exists(storage_path('app/maintenance_mode'));
    }

    /**
     * En mode maintenance : seul le Fondateur principal peut accéder au site.
     * Les autres voient la page maintenance. La page de connexion reste accessible.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! File::exists($this->maintenanceFilePath())) {
            return $next($request);
        }

        if ($request->is('login')) {
            return $next($request);
        }

        $user = $request->user();
        if ($user && $user->isFondateurPrincipal()) {
            return $next($request);
        }

        return response()->view('maintenance', [], 503);
    }
}
