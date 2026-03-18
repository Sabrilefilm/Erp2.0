<?php

namespace App\Jobs;

use App\Models\NotificationTemplate;
use App\Services\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $body,
        public string $targetType,
        public ?string $targetValue,
        public ?int $sentBy = null,
        public ?string $templateKey = null,
        public array $payload = []
    ) {}

    public function handle(WebPushService $push): void
    {
        if ($this->templateKey) {
            $template = NotificationTemplate::where('key', $this->templateKey)->where('active', true)->first();
            if ($template) {
                $this->title = $template->title;
                $this->body = $template->body;
            }
        }

        $push->sendAndLog(
            $this->title,
            $this->body,
            $this->targetType,
            $this->targetValue,
            $this->sentBy,
            null,
            $this->payload
        );
    }
}
