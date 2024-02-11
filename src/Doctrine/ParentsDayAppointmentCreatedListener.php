<?php

namespace App\Doctrine;

use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Event\ParentsDayAppointmentCancelledEvent;
use App\Event\ParentsDayAppointmentCreatedEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::preUpdate)]
class ParentsDayAppointmentCreatedListener {

    public function __construct(private readonly DoctrineEventsCollector $collector) {

    }

    public function preUpdate(PreUpdateEventArgs $args): void {
        if(!$args->getObject() instanceof ParentsDayAppointment) {
            return;
        }

        $uow = $args->getObjectManager()->getUnitOfWork();

        foreach($uow->getScheduledCollectionUpdates() as $collectionUpdate) {
            $owner = $collectionUpdate->getOwner();

            if(!$owner instanceof ParentsDayAppointment) {
                continue;
            }

            /** @var Student|Teacher $studentOrTeacher */
            foreach($collectionUpdate->getInsertDiff() as $studentOrTeacher) {
                if($studentOrTeacher instanceof Student) {
                    $this->collector->collect(new ParentsDayAppointmentCreatedEvent($owner, $studentOrTeacher));
                }
            }
        }
    }

}