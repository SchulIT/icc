<?php

namespace App\Notification;

use App\Event\AppointmentConfirmedEvent;
use App\Event\ExamImportEvent;
use App\Event\MessageCreatedEvent;
use App\Event\MessageUpdatedEvent;
use App\Event\SubstitutionImportEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\EmailStrategyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationService implements EventSubscriberInterface {
    /**
     * @param EmailStrategyInterface[] $emailStrategies
     */
    public function __construct(private EmailNotificationService $email, private iterable $emailStrategies)
    {
    }

    /**
     * Sends notifications based on the given objective.
     *
     * @param object $objective
     */
    public function sendNotifications($objective) {
        foreach($this->emailStrategies as $strategy) {
            if($strategy->isEnabled() && $strategy->supports($objective)) {
                $this->email->sendNotification($objective, $strategy);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            MessageCreatedEvent::class => 'sendNotifications',
            MessageUpdatedEvent::class => 'sendNotifications',
            ExamImportEvent::class => 'sendNotifications',
            SubstitutionImportEvent::class => 'sendNotifications',
            AppointmentConfirmedEvent::class => 'sendNotifications'
        ];
    }
}