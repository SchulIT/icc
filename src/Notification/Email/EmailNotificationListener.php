<?php

namespace App\Notification\Email;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailNotificationListener implements EventSubscriberInterface {



    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [ ];
    }
}