<?php

namespace App\Doctrine;

use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Event\ParentsDayAppointmentCancelledEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork;
use LogicException;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
class ParentsDayAppointmentCancelledListener {

    public function __construct(private readonly DoctrineEventsCollector $collector, private readonly TokenStorageInterface $tokenStorage) {

    }

    private function getUser(): User {
        $user = $this->tokenStorage->getToken()->getUser();

        if($user instanceof User) {
            return $user;
        }

        throw new LogicException('This code should not be executed.');
    }

    public function preRemove(PreRemoveEventArgs $args): void {
        $appointment = $args->getObject();
        if(!$appointment instanceof ParentsDayAppointment) {
            return;
        }

        foreach($appointment->getStudents() as $student) {
            $this->collector->collect(new ParentsDayAppointmentCancelledEvent($appointment, $student, $this->getUser()));
        }
    }


    public function preUpdate(PreUpdateEventArgs $args): void {
        $appointment = $args->getObject();

        if(!$appointment instanceof ParentsDayAppointment) {
            return;
        }

        if($args->hasChangedField('isCancelled')) {
            foreach($appointment->getStudents() as $student) {
                $this->collector->collect(new ParentsDayAppointmentCancelledEvent($appointment, $student, $this->getUser()));
            }
        }

        $uow = $args->getObjectManager()->getUnitOfWork();

        foreach($uow->getScheduledCollectionDeletions() as $collectionDeletion) {
            $owner = $collectionDeletion->getOwner();

            if(!$owner instanceof ParentsDayAppointment) {
                continue;
            }

            $this->handleCollectionChange($collectionDeletion, $uow, $owner);
        }

        foreach($uow->getScheduledCollectionUpdates() as $collectionUpdate) {
            $owner = $collectionUpdate->getOwner();

            if(!$owner instanceof ParentsDayAppointment) {
                continue;
            }

            $this->handleCollectionChange($collectionUpdate, $uow, $owner);
        }
    }

    private function handleCollectionChange(PersistentCollection $collection, UnitOfWork $uow, ParentsDayAppointment $appointment): void {
        $deleted = $collection->getDeleteDiff();

        /*
         * See https://stackoverflow.com/a/75277454
         */

        if(count($deleted) === 0) {
            $clone = clone $collection;
            $clone->setOwner($collection->getOwner(), $collection->getMapping());
            $uow->loadCollection($clone);
            $deleted = $clone->toArray();
        }

        /** @var Student|Teacher $studentOrTeacher */
        foreach($deleted as $studentOrTeacher) {
            if($studentOrTeacher instanceof Student) {
                $this->collector->collect(new ParentsDayAppointmentCancelledEvent($appointment, $studentOrTeacher, $this->getUser()));
            }
        }
    }

}