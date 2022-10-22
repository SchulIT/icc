<?php

namespace App\Doctrine;

use App\Entity\StudentAbsenceMessage;
use App\Event\StudentAbsenceMessageCreatedEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StudentAbsenceMessagePersistSubscriber implements EventSubscriber {

    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function postPersist(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();

        if($entity instanceof StudentAbsenceMessage) {
            $this->dispatcher->dispatch(new StudentAbsenceMessageCreatedEvent($entity));
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array {
        return [
            Events::postPersist
        ];
    }
}