<?php

namespace App\Appointment\Doctrine;

use App\Appointment\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Ramsey\Uuid\Uuid;

#[AsEntityListener(event: Events::prePersist, entity: Appointment::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Appointment::class)]
class AppointmentRecurringIdSubscriber {
    public function prePersist(Appointment $entity, PrePersistEventArgs $args): void {
        if($entity->isRecurring()) {
            $entity->setRecurringUuid(Uuid::uuid4());
        }
    }

    public function preUpdate(Appointment $entity, PreUpdateEventArgs $args): void {
        if($args->hasChangedField('isRecurring') && $entity->isRecurring() && $entity->getRecurringUuid() === null) {
            $entity->setRecurringUuid(Uuid::uuid4());

            $uow = $args->getObjectManager()->getunitOfWork();
            $uow->recomputeSingleEntityChangeSet($args->getObjectManager()->getClassMetadata(Appointment::class), $entity);
        }
    }
}
