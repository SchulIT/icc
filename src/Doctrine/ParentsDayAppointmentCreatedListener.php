<?php

namespace App\Doctrine;

use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Event\ParentsDayAppointmentCancelledEvent;
use App\Event\ParentsDayAppointmentCreatedEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsDoctrineListener(event: Events::preUpdate)]
class ParentsDayAppointmentCreatedListener {

    public function __construct(private readonly DoctrineEventsCollector $collector, private readonly TokenStorageInterface $tokenStorage) {

    }

    private function getUser(): User {
        $user = $this->tokenStorage->getToken()->getUser();

        if($user instanceof User) {
            return $user;
        }

        throw new LogicException('This code should not be executed.');
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
                    $this->collector->collect(new ParentsDayAppointmentCreatedEvent($owner, $studentOrTeacher, $this->getUser()));
                }
            }
        }
    }

}