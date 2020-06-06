<?php

namespace App\EventSubscriber;

use App\Event\ExamImportEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\ExamStrategy as EmailStrategy;
use App\Notification\WebPush\PushNotificationService;
use App\Notification\WebPush\ExamStrategy as PushStrategy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExamsImportedSubscriber implements EventSubscriberInterface {

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

    public function onExamsImported(ExamImportEvent $event) {
        $this->emailNotificationService->sendNotification(null, $this->emailNotificationStrategy);
        $this->pushNotificationService->sendNotifications(null, $this->pushNotificationStrategy);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            ExamImportEvent::class => 'onExamsImported'
        ];
    }
}