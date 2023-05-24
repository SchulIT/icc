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

class NotificationService {
    private readonly iterable $handler;

    /**
     * @param NotificationHandlerInterface[] $handler
     */
    public function __construct(iterable $handler) {
        $this->handler = $handler;
    }

    /**
     * @param Notification $notification
     * @param string[] $handlersToBeExecuted If specified, only the given handlers are executed (this is useful if a notification is only supposed to be delivered by email)
     * @return void
     */
    public function notify(Notification $notification, array $handlersToBeExecuted = [ ]): void {
        foreach($this->handler as $handler) {
            if((empty($handlersToBeExecuted) || in_array($handler->getName(), $handlersToBeExecuted)) && $handler->canHandle($notification)) {
                $handler->handle($notification);
            }
        }
    }
}