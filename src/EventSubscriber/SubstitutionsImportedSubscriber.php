<?php

namespace App\EventSubscriber;

use App\Event\SubstitutionImportEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\SubstitutionStrategy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubstitutionsImportedSubscriber implements EventSubscriberInterface {

    private $emailNotificationService;
    private $strategy;

    public function __construct(EmailNotificationService $emailNotificationService, SubstitutionStrategy $strategy) {
        $this->emailNotificationService = $emailNotificationService;
        $this->strategy = $strategy;
    }

    public function onSubstitutionsImported(SubstitutionImportEvent $event) {
        $this->emailNotificationService->sendNotification(null, $this->strategy);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            SubstitutionImportEvent::class => 'onSubstitutionsImported'
        ];
    }
}