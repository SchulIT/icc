<?php

namespace App\ParentsDay\Doctrine;

use App\ParentsDay\Entity\ParentsDayAppointment;
use App\Common\Entity\Student;
use App\Common\Entity\Teacher;
use App\Common\Entity\User;
use App\ParentsDay\Event\ParentsDayAppointmentCancelledEvent;
use App\ParentsDay\Event\ParentsDayAppointmentCreatedEvent;
use App\Infrastructure\EventSubscriber\DoctrineEventsCollector;
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