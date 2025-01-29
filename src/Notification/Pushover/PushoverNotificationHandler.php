<?php

namespace App\Notification\Pushover;

use App\Messenger\SendPushoverNotificationMessage;
use App\Notification\Delivery\DeliveryDecider;
use App\Notification\Notification;
use App\Notification\NotificationDeliveryTarget;
use App\Notification\NotificationHandlerInterface;
use App\Settings\NotificationSettings;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class PushoverNotificationHandler implements NotificationHandlerInterface {

    public function __construct(#[Autowire(env: 'PUSHOVER_TOKEN')] private ?string $pushoverToken,
                                private NotificationSettings $notificationSettings,
                                private DeliveryDecider $deliveryDecider,
                                private MessageBusInterface $messageBus) { }

    public function canHandle(Notification $notification): bool {
        return !empty($this->pushoverToken)
            && $this->notificationSettings->isNotificationsEnabled()
            && $this->notificationSettings->isPushoverEnabled()
            && !empty($notification->getRecipient()->getPushoverToken());
    }

    public function handle(Notification $notification): void {
        // Check delivery options
        if($notification->isDeliveryEnforced() === false ||  $this->deliveryDecider->decide($notification->getRecipient(), $notification->getType(), NotificationDeliveryTarget::Pushover) !== true) {
            return;
        }

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