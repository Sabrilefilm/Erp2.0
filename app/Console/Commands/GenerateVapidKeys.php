<?php

namespace App\Console\Commands;

use Minishlink\WebPush\VAPID;
use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'push:generate-vapid-keys';

    protected $description = 'Genere les cles VAPID pour les notifications push (a copier dans .env).';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();
        $this->line('Ajoutez ces lignes dans votre fichier .env :');
        $this->newLine();
        $this->line('WEBPUSH_VAPID_SUBJECT="mailto:admin@votredomaine.com"');
        $this->line('WEBPUSH_VAPID_PUBLIC_KEY="' . $keys['publicKey'] . '"');
        $this->line('WEBPUSH_VAPID_PRIVATE_KEY="' . $keys['privateKey'] . '"');
        $this->newLine();
        $this->info('Ne partagez jamais la cle privee. Conservez ces cles pour toute la duree du projet.');
        return self::SUCCESS;
    }
}
