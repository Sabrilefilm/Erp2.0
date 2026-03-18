<?php

namespace App\Notifications;

use App\Models\DemandeMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemandeMatchNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DemandeMatch $demande
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->demande->load('createur');
        $createurNom = $this->demande->createur?->nom ?? 'Un créateur';
        $typeLabel = \App\Http\Controllers\MatchController::MATCH_TYPES[$this->demande->type] ?? $this->demande->type;
        $date = $this->demande->date_souhaitee->translatedFormat('d/m/Y');
        $heure = $this->demande->heure_souhaitee ? ' à ' . substr($this->demande->heure_souhaitee, 0, 5) : '';

        return [
            'message' => "{$createurNom} demande un match ({$typeLabel}) le {$date}{$heure}. À traiter dans Matchs.",
            'type' => 'demande_match',
            'demande_match_id' => $this->demande->id,
            'createur_id' => $this->demande->createur_id,
            'url' => route('matches.index'),
        ];
    }
}
