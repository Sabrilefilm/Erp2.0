<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => NotificationTemplate::KEY_MATCH_OFF_REMINDER,
                'label' => 'Rappel match off',
                'title' => 'Match off',
                'body' => 'Ton match off commence dans 30 minutes.',
                'active' => true,
            ],
            [
                'key' => NotificationTemplate::KEY_RAPPEL_LIVE,
                'label' => 'Rappel de live',
                'title' => 'Rappel live',
                'body' => 'Ton live commence bientot. Prepare-toi !',
                'active' => true,
            ],
            [
                'key' => NotificationTemplate::KEY_OBJECTIF_ATTEINT,
                'label' => 'Objectif atteint',
                'title' => 'Objectif atteint',
                'body' => 'Felicitations, tu as atteint ton objectif !',
                'active' => true,
            ],
            [
                'key' => NotificationTemplate::KEY_ANNONCE,
                'label' => 'Annonce importante',
                'title' => 'Annonce',
                'body' => 'Une annonce importante vous attend sur l\'application.',
                'active' => true,
            ],
        ];

        foreach ($templates as $t) {
            NotificationTemplate::updateOrCreate(
                ['key' => $t['key']],
                $t
            );
        }
    }
}
