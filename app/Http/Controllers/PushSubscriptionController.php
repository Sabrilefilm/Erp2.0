<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    /**
     * Retourne la clé publique VAPID pour l'enregistrement côté client.
     */
    public function publicKey(): JsonResponse
    {
        $key = config('webpush.vapid.public_key');
        if (empty($key)) {
            return response()->json(['error' => 'Push non configuré'], 503);
        }
        return response()->json(['publicKey' => $key]);
    }

    /**
     * Enregistre ou met à jour un abonnement push pour l'utilisateur connecté.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string|max:512',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = $request->user();
        $endpoint = $request->input('endpoint');
        $keys = $request->input('keys');

        $sub = PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
            ],
            [
                'public_key' => $keys['p256dh'],
                'auth_token' => $keys['auth'],
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json(['success' => true, 'id' => $sub->id]);
    }

    /**
     * Supprime un abonnement (même endpoint).
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required|string|max:512']);
        $deleted = PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint', $request->input('endpoint'))
            ->delete();
        return response()->json(['success' => $deleted > 0]);
    }
}
