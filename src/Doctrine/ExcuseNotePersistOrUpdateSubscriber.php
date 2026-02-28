<?php

namespace App\Doctrine;

use App\Book\Excuse\AssociateAttendanceMessage;
use App\Book\Excuse\AssociateExcuseNoteMessage;
use App\Entity\Attendance;
use App\Entity\ExcuseNote;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
readonly class ExcuseNotePersistOrUpdateSubscriber {
    public function __construct(
        private MessageBusInterface $messageBus
    ) {

    }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if(!$entity instanceof ExcuseNote) {
            return;
        }

        $this->messageBus->dispatch(new AssociateExcuseNoteMessage($entity->getId()));
    }

    public function postUpdate(PostUpdateEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if(!$entity instanceof ExcuseNote) {
            return;
        }

        $uow = $eventArgs->getObjectManager()->getUnitOfWork();
        $changeset = $uow->getEntityChangeSet($entity);

        if(count($changeset) === 0) {
            return;
        }

        $this->messageBus->dispatch(new AssociateExcuseNoteMessage($entity->getId()));
    }
}