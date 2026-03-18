<?php

namespace App\Notifications;

use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RappelMatchNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Planning $planning
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $typeLabel = \App\Http\Controllers\MatchController::MATCH_TYPES[$this->planning->type] ?? $this->planning->type;
        $heure = $this->planning->heure ? substr($this->planning->heure, 0, 5) : '—';
        $date = $this->planning->date->translatedFormat('d/m/Y');

        return [
            'message' => "⏰ Lance ton match dans 30 min — {$typeLabel} le {$date} à {$heure}. Prépare-toi !",
            'type' => 'rappel_match',
            'planning_id' => $this->planning->id,
            'date' => $this->planning->date->toDateString(),
            'heure' => $this->planning->heure,
            'match_type' => $typeLabel,
            'url' => route('matches.index'),
        ];
    }
}
