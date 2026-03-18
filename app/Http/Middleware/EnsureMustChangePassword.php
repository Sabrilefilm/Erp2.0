<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMustChangePassword
{
    /**
     * Si l'utilisateur doit changer son mot de passe (après déblocage temporaire), le rediriger vers la page dédiée.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        if ($request->routeIs('password.change-required') || $request->routeIs('password.change-required.store')) {
            return $next($request);
        }

        return redirect()->route('password.change-required');
    }
}
