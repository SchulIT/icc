<?php

namespace App\EventSubscriber;

use App\Event\SubstitutionImportEvent;
use App\Notification\Email\EmailNotificationService;
use App\Notification\Email\SubstitutionStrategy as EmailStrategy;
use App\Notification\WebPush\PushNotificationService;
use App\Notification\WebPush\SubstitutionStrategy as PushStrategy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubstitutionsImportedSubscriber implements EventSubscriberInterface {

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

    public function onSubstitutionsImported(SubstitutionImportEvent $event) {
        $this->emailNotificationService->sendNotification(null, $this->emailNotificationStrategy);
        $this->pushNotificationService->sendNotifications(null, $this->pushNotificationStrategy);
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