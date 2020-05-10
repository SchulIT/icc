<?php

namespace App\EventSubscriber;

use App\Event\ExamImportEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\ExamStrategy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExamsImportedSubscriber implements EventSubscriberInterface {

    private $emailNotificationService;
    private $strategy;

    public function __construct(EmailNotificationService $emailNotificationService, ExamStrategy $strategy) {
        $this->emailNotificationService = $emailNotificationService;
        $this->strategy = $strategy;
    }

    public function onExamsImported(ExamImportEvent $event) {
        $this->emailNotificationService->sendNotification(null, $this->strategy);
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