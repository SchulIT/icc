<?php

namespace App\Notification\Pushover;

use App\Messenger\SendPushoverNotificationMessage;
use App\Notification\Notification;
use App\Notification\NotificationHandlerInterface;
use App\Settings\NotificationSettings;
use App\Utils\ArrayUtils;
use Symfony\Component\Messenger\MessageBusInterface;

class PushoverNotificationHandler implements NotificationHandlerInterface {

    public function __construct(private readonly ?string $pushoverToken, private readonly NotificationSettings $notificationSettings, private readonly MessageBusInterface $messageBus) { }

    public function canHandle(Notification $notification): bool {
        return !empty($this->pushoverToken)
            && !empty($notification->getRecipient()->getPushoverToken())
            && ArrayUtils::inArray($notification->getRecipient()->getUserType(), $this->notificationSettings->getPushoverEnabledUserTypes()) !== false;
    }

    public function handle(Notification $notification): void {
        $this->messageBus->dispatch(
            new SendPushoverNotificationMessage(
                $notification->getRecipient()->getId(),
                $notification->getRecipient()->getUserIdentifier(),
                $notification->getContent(),
                $notification->getSubject(),
                $notification->getLink(),
                $notification->getLinkText()
            )
        );
    }

    public function getName(): string {
        return 'pushover';
    }
}