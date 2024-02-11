<?php

namespace App\Doctrine;

use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Event\ParentsDayAppointmentCancelledEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
class ParentsDayAppointmentCancelledListener {

    public function __construct(private readonly DoctrineEventsCollector $collector) {

    }

    public function preRemove(PreRemoveEventArgs $args): void {
        $appointment = $args->getObject();
        if(!$appointment instanceof ParentsDayAppointment) {
            return;
        }

        foreach($appointment->getStudents() as $student) {
            $this->collector->collect(new ParentsDayAppointmentCancelledEvent($appointment, $student));
        }
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
            foreach($collectionUpdate->getDeleteDiff() as $studentOrTeacher) {
                if($studentOrTeacher instanceof Student) {
                    $this->collector->collect(new ParentsDayAppointmentCancelledEvent($owner, $studentOrTeacher));
                }
            }
        }
    }

}