<?php

namespace App\Doctrine;

use App\Entity\Appointment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Imitates the @Gedmo\Blameable() listener so it only sets the createdBy attribute if
 * the current user is an actual user (of type App\Entity\User).
 */
#[AsDoctrineListener(event: Events::prePersist)]
class AppointmentBlameableSubscriber {

    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void {
        $entity = $args->getObject();
        $token = $this->tokenStorage->getToken();

        if($entity instanceof Appointment && $token !== null) {
            $user = $token->getUser();

            if($user instanceof User) {
                $entity->setCreatedBy($user);
            }
        }
    }
}