<?php

namespace App\EventSubscriber;

use App\Event\MessageCreatedEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\MessageStrategy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageCreatedSubscriber implements EventSubscriberInterface {

    private $emailNotificationService;
    private $strategy;

    public function __construct(EmailNotificationService $emailNotificationService, MessageStrategy $strategy) {
        $this->emailNotificationService = $emailNotificationService;
        $this->strategy = $strategy;
    }

    public function onMessageCreated(MessageCreatedEvent $event) {
        $this->emailNotificationService->sendNotification($event->getMessage(), $this->strategy);
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