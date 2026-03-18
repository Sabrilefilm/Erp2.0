<?php

namespace App\Services;

use App\Models\PushNotificationLog;
use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription as WebPushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\SubscriptionInterface;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $config = config('webpush');
        $vapid = $config['vapid'] ?? [];
        if (! empty($vapid['public_key']) && ! empty($vapid['private_key'])) {
            $this->webPush = new WebPush([
                'VAPID' => [
                    'subject' => $vapid['subject'] ?? 'mailto:admin@unions-agency.com',
                    'publicKey' => $vapid['public_key'],
                    'privateKey' => $vapid['private_key'],
                ],
            ], [
                'TTL' => $config['ttl'] ?? 86400,
            ]);
        }
    }

    public function isConfigured(): bool
    {
        return $this->webPush !== null;
    }

    /**
     * Envoie une notification push à une liste d'abonnements.
     *
     * @param  array<int, PushSubscription>  $subscriptions
     * @return array{sent: int, failed: int, expired: int}
     */
    public function sendToSubscriptions(array $subscriptions, string $title, string $body, array $payload = []): array
    {
        if (! $this->webPush) {
            return ['sent' => 0, 'failed' => count($subscriptions), 'expired' => 0];
        }

        $message = json_encode(array_merge(
            ['title' => $title, 'body' => $body],
            $payload
        ), JSON_THROW_ON_ERROR);

        $expired = 0;
        $sent = 0;

        foreach ($subscriptions as $sub) {
            $wpSub = $this->toWebPushSubscription($sub);
            if (! $wpSub) {
                continue;
            }
            try {
                $this->webPush->queueNotification($wpSub, $message);
            } catch (\Throwable) {
                continue;
            }
        }

        foreach ($this->webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } else {
                $reason = $report->getReason();
                if ($report->isSubscriptionExpired() || ($reason && (str_contains((string) $reason, '410') || str_contains((string) $reason, '404')))) {
                    $expired++;
                }
            }
        }

        return [
            'sent' => $sent,
            'failed' => count($subscriptions) - $sent - $expired,
            'expired' => $expired,
        ];
    }

    /**
     * Récupère les abonnements selon la cible (user, role, all).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, PushSubscription>
     */
    public function getSubscriptionsForTarget(string $targetType, ?string $targetValue = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = PushSubscription::query()->with('user');

        if ($targetType === 'user' && $targetValue) {
            $query->where('user_id', $targetValue);
        } elseif ($targetType === 'role' && $targetValue) {
            $query->whereHas('user', fn ($q) => $q->where('role', $targetValue));
        }
        // 'all' => pas de filtre

        return $query->get();
    }

    /**
     * Envoie une notification et enregistre le log.
     *
     * @param  array<int, PushSubscription>|null  $subscriptions  Si null, calculé depuis target_type / target_value
     */
    public function sendAndLog(
        string $title,
        string $body,
        string $targetType,
        ?string $targetValue,
        ?int $sentBy = null,
        ?array $subscriptions = null,
        array $payload = []
    ): PushNotificationLog {
        $subs = $subscriptions ?? $this->getSubscriptionsForTarget($targetType, $targetValue)->all();
        $result = $this->sendToSubscriptions($subs, $title, $body, $payload);

        $log = PushNotificationLog::create([
            'title' => $title,
            'body' => $body,
            'target_type' => $targetType,
            'target_value' => $targetValue,
            'recipients_count' => $result['sent'],
            'opened_count' => 0,
            'sent_by' => $sentBy,
            'sent_at' => now(),
            'payload' => $payload ?: null,
        ]);

        $this->removeExpiredSubscriptions($subs, $result['expired']);

        return $log;
    }

    protected function toWebPushSubscription(PushSubscription $sub): ?SubscriptionInterface
    {
        if (! $sub->endpoint || ! $sub->public_key || ! $sub->auth_token) {
            return null;
        }
        return WebPushSubscription::create([
            'endpoint' => $sub->endpoint,
            'keys' => [
                'p256dh' => $sub->public_key,
                'auth' => $sub->auth_token,
            ],
        ]);
    }

    /**
     * Supprime les abonnements expirés (410/404). On ne peut pas savoir lesquels sans rapport détaillé,
     * donc on ne supprime pas automatiquement ici ; un job de nettoyage peut s'appuyer sur les rapports.
     */
    protected function removeExpiredSubscriptions(array $subscriptions, int $expiredCount): void
    {
        // Optionnel : si on veut supprimer après coup, il faudrait que sendToSubscriptions
        // retourne les IDs des abonnements expirés. Pour l'instant on laisse tel quel.
    }
}
