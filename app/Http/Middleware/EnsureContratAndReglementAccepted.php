<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureContratAndReglementAccepted
{
    /**
     * Les créateurs doivent avoir signé le contrat ET accepté le règlement pour accéder au reste de l'application.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isCreateur()) {
            return $next($request);
        }

        $createur = \App\Models\Createur::where('user_id', $user->id)->first();
        if (! $createur) {
            return $next($request);
        }

        $contratOk = $createur->contrat_signe_le !== null;
        $reglementOk = $createur->reglement_accepte_le !== null;

        if ($contratOk && $reglementOk) {
            return $next($request);
        }

        if ($request->routeIs('documents-officiels.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('documents-officiels.index')
            ->with('warning', 'Vous devez signer le contrat et accepter le règlement intérieur pour accéder à l\'application.');
    }
}
