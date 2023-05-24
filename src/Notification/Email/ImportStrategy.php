<?php

namespace App\Notification\Email;

use App\Notification\ImportNotification;
use App\Notification\Notification;

class ImportStrategy implements EmailStrategyInterface {

    public function __construct(private readonly string $appName) { }

    public function supports(Notification $notification): bool {
        return $notification instanceof ImportNotification;
    }

    /**
     * @param ImportNotification $notification
     * @return string|null
     */
    public function getReplyTo(Notification $notification): ?string {
        return $notification->getReplyTo();
    }

    /**
     * @param ImportNotification $notification
     * @return string
     */
    public function getSender(Notification $notification): string {
        return !empty($notification->getSender()) ? $notification->getSender() : $this->appName;
    }

    public function getTemplate(): string {
        return 'email/default.txt.twig';
    }

    public function getHtmlTemplate(): ?string {
        return 'email/default.html.twig';
    }
}