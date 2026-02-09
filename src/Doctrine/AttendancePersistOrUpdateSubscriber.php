<?php

namespace App\Doctrine;

use App\Book\Excuse\AssociateAttendanceMessage;
use App\Entity\Attendance;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
readonly class AttendancePersistOrUpdateSubscriber {
    public function __construct(
        private MessageBusInterface $messageBus
    ) {

    }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if(!$entity instanceof Attendance) {
            return;
        }

        $this->messageBus->dispatch(new AssociateAttendanceMessage($entity->getId()));
    }

    public function postUpdate(PostUpdateEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if(!$entity instanceof Attendance) {
            return;
        }

        $this->messageBus->dispatch(new AssociateAttendanceMessage($entity->getId()));
    }
}