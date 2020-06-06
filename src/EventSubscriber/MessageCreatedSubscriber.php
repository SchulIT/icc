<?php

namespace App\EventSubscriber;

use App\Event\MessageCreatedEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\MessageStrategy as EmailStrategy;
use App\Notification\WebPush\MessageStrategy as PushStrategy;
use App\Notification\WebPush\PushNotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageCreatedSubscriber implements EventSubscriberInterface {

    private $emailNotificationService;
    private $emailNotificationStrategy;

    private $pushNotificationService;
    private $pushNotificationStrategy;

    public function __construct(EmailNotificationService $emailNotificationService, EmailStrategy $emailStrategy,
                                PushNotificationService $pushNotificationService, PushStrategy $pushStrategy) {
        $this->emailNotificationService = $emailNotificationService;
        $this->emailNotificationStrategy = $emailStrategy;

        $this->pushNotificationService = $pushNotificationService;
        $this->pushNotificationStrategy = $pushStrategy;
    }

    public function onMessageCreated(MessageCreatedEvent $event) {
        $this->emailNotificationService->sendNotification($event->getMessage(), $this->emailNotificationStrategy);
        $this->pushNotificationService->sendNotifications($event->getMessage(), $this->pushNotificationStrategy);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            MessageCreatedEvent::class => 'onMessageCreated'
        ];
    }
}