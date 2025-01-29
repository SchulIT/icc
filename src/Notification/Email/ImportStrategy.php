<?php

namespace App\Notification\Email;

use App\Notification\ImportNotification;
use App\Notification\Notification;
use App\Settings\NotificationSettings;
use App\Utils\ArrayUtils;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class ImportStrategy implements EmailStrategyInterface {

    public function __construct(#[Autowire(env: 'APP_NAME')] private string $appName,
                                private NotificationSettings $notificationSettings) { }

    public function supports(Notification $notification): bool {
        return $notification instanceof ImportNotification
            // Da es sich um eine Massen-Benachrichtigung handelt, sind nur ausgewählte Benutzertypen erlaubt
            && ArrayUtils::inArray($notification->getRecipient()->getUserType(), $this->notificationSettings->getEmailEnabledUserTypes()) !== false;
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