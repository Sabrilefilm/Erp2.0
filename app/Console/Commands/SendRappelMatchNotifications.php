<?php

namespace App\Console\Commands;

use App\Models\Planning;
use App\Models\User;
use App\Notifications\RappelMatchNotification;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRappelMatchNotifications extends Command
{
    protected $signature = 'matchs:send-rappels';

    protected $description = 'Envoie les notifications de rappel 30 min avant chaque match aux créateurs.';

    public function handle(): int
    {
        $now = Carbon::now();
        $windowStart = $now->copy()->addMinutes(29);
        $windowEnd = $now->copy()->addMinutes(31);

        $matchs = Planning::whereNull('rappel_sent_at')
            ->whereNotNull('heure')
            ->where('date', '>=', $now->toDateString())
            ->get();

        $sent = 0;
        foreach ($matchs as $planning) {
            $matchDt = Carbon::parse($planning->date->format('Y-m-d') . ' ' . $planning->heure);
            if ($matchDt->lt($windowStart) || $matchDt->gt($windowEnd)) {
                continue;
            }

            $createur = $planning->createur;
            $user = $createur->user_id
                ? $createur->user
                : User::where('email', $createur->email)->where('role', User::ROLE_CREATEUR)->first();

            if ($user) {
                $user->notify(new RappelMatchNotification($planning));
                $push = app(WebPushService::class);
                if ($push->isConfigured()) {
                    $subs = $push->getSubscriptionsForTarget('user', (string) $user->id);
                    if ($subs->isNotEmpty()) {
                        $push->sendToSubscriptions(
                            $subs->all(),
                            'Match off',
                            'Ton match off commence dans 30 minutes.',
                            ['url' => route('matches.index')]
                        );
                    }
                }
                $planning->update(['rappel_sent_at' => $now]);
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->info("Rappels envoyés : {$sent}.");
        }

        return self::SUCCESS;
    }
}
