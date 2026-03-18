<?php

namespace App\Notifications;

use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchProgrammeNotification extends Notification
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
        $date = $this->planning->date->translatedFormat('l j F Y');
        $heure = $this->planning->heure ? ' à ' . $this->planning->heure : '';

        return [
            'message' => "Un match ({$typeLabel}) a été programmé pour vous le {$date}{$heure}.",
            'type' => 'match_programme',
            'planning_id' => $this->planning->id,
            'date' => $this->planning->date->toDateString(),
            'heure' => $this->planning->heure,
            'match_type' => $typeLabel,
            'url' => route('matches.index'),
        ];
    }
}
