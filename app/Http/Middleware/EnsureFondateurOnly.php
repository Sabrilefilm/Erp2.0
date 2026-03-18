<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFondateurOnly
{
    /**
     * SEUL LE FONDATEUR PRINCIPAL peut accéder (import Excel, gestion infractions, etc.).
     * Le fondateur d’une sous-agence n’a pas ce pouvoir.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->isFondateurPrincipal()) {
            abort(403, 'Accès réservé au Fondateur principal.');
        }

        return $next($request);
    }
}
