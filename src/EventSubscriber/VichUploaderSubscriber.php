<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

/**
 * Removes the file entity which uses @Vich\Uploadable() from the database.
 */
class VichUploaderSubscriber implements EventSubscriberInterface {

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function postRemoveFile(Event $event): void {
        $entity = $event->getObject();

        if($this->em->contains($entity)) { // only remove from database if not already removed
            $this->em->remove($entity);
            $this->em->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            Events::POST_REMOVE => [
                [ 'postRemoveFile', 0]
            ]
        ];
    }


}