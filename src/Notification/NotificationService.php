<?php

namespace App\Notification;

use App\Event\ExamImportEvent;
use App\Event\MessageCreatedEvent;
use App\Event\MessageUpdatedEvent;
use App\Event\SubstitutionImportEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\EmailStrategyInterface;
use App\Notification\WebPush\PushNotificationService;
use App\Notification\WebPush\PushNotificationStrategyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationService implements EventSubscriberInterface {
    private $email;
    private $push;

    /** @var EmailStrategyInterface[] */
    private $emailStrategies;

    /** @var PushNotificationStrategyInterface[] */
    private $pushStrategies;

    public function __construct(EmailNotificationService $email, PushNotificationService $push, iterable $emailStrategies, iterable $pushStrategies) {
        $this->email = $email;
        $this->push = $push;
        $this->emailStrategies = $emailStrategies;
        $this->pushStrategies = $pushStrategies;
    }

    /**
     * Sends notifications based on the given objective.
     *
     * @param $objective
     */
    public function sendNotifications($objective) {
        foreach($this->emailStrategies as $strategy) {
            if($strategy->isEnabled() && $strategy->supports($objective)) {
                $this->email->sendNotification($objective, $strategy);
            }
        }

        foreach($this->pushStrategies as $strategy) {
            if($strategy->supports($objective)) {
                $this->push->sendNotifications($objective, $strategy);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            MessageCreatedEvent::class => 'sendNotifications',
            MessageUpdatedEvent::class => 'sendNotifications',
            ExamImportEvent::class => 'sendNotifications',
            SubstitutionImportEvent::class => 'sendNotifications'
        ];
    }
}