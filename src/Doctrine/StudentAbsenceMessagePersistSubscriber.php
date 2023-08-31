<?php

namespace App\Doctrine;

use App\Entity\StudentAbsenceMessage;
use App\Event\StudentAbsenceMessageCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsDoctrineListener(event: Events::postPersist)]
class StudentAbsenceMessagePersistSubscriber {

    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof StudentAbsenceMessage) {
            $this->dispatcher->dispatch(new StudentAbsenceMessageCreatedEvent($entity));
        }
    }
}