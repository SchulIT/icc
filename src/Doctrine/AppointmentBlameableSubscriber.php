<?php

namespace App\Doctrine;

use App\Entity\Appointment;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Imitates the @Gedmo\Blameable() listener so it only sets the createdBy attribute if
 * the current user is an actual user (of type App\Entity\User).
 */
class AppointmentBlameableSubscriber implements EventSubscriber {

    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $token = $this->tokenStorage->getToken();

        if($entity instanceof Appointment && $token !== null) {
            $user = $token->getUser();

            if($user instanceof User) {
                $entity->setCreatedBy($user);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array {
        return [
            Events::prePersist
        ];
    }
}