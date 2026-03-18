<?php

namespace App\Console\Commands;

use App\Jobs\SendPushNotificationJob;
use App\Models\ScheduledPushNotification;
use Illuminate\Console\Command;

class ProcessScheduledPushNotifications extends Command
{
    protected $signature = 'push:process-scheduled';

    protected $description = 'Envoie les notifications push dont l\'heure est atteinte.';

    public function handle(): int
    {
        $due = ScheduledPushNotification::whereNull('sent_at')
            ->where('send_at', '<=', now())
            ->get();

        foreach ($due as $scheduled) {
            SendPushNotificationJob::dispatch(
                $scheduled->title,
                $scheduled->body ?? '',
                $scheduled->target_type,
                $scheduled->target_value,
                $scheduled->created_by,
                $scheduled->template_key,
                []
            );
            $scheduled->update(['sent_at' => now()]);
        }

        if ($due->isNotEmpty()) {
            $this->info('Envoi de ' . $due->count() . ' notification(s) planifiee(s).');
        }

        return self::SUCCESS;
    }
}
