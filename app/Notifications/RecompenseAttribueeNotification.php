<?php

namespace App\Notifications;

use App\Models\Recompense;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecompenseAttribueeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Recompense $recompense
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $montant = number_format((float) $this->recompense->montant, 2, ',', ' ');
        $raison = $this->recompense->raison ? " — {$this->recompense->raison}" : '';

        return [
            'message' => "Une récompense de {$montant} € vous a été attribuée{$raison}. Choisissez votre mode de réception (virement, TikTok ou carte cadeau) sur la page Récompenses.",
            'type' => 'recompense_attribuee',
            'recompense_id' => $this->recompense->id,
            'url' => route('recompenses.index'),
        ];
    }
}
