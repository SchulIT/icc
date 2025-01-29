<?php

namespace App\Notification;

use App\Settings\NotificationSettings;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class NotificationService {

    /**
     * @param NotificationHandlerInterface[] $handler
     */
    public function __construct(#[AutowireIterator('app.notifications.handler')] private iterable $handler,
                                private NotificationSettings $settings) {    }

    /**
     * @param Notification $notification
     * @param string[] $handlersToBeExecuted If specified, only the given handlers are executed (this is useful if a notification is only supposed to be delivered by email)
     * @return void
     */
    public function notify(Notification $notification, array $handlersToBeExecuted = [ ]): void {
        if($this->settings->isNotificationsEnabled() !== true) {
            return;
        }

        foreach($this->handler as $handler) {
            if((empty($handlersToBeExecuted) || in_array($handler->getName(), $handlersToBeExecuted)) && $handler->canHandle($notification)) {
                $handler->handle($notification);
            }
        }
    }
}