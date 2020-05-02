<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

/**
 * Removes the file entity which uses @Vich\Uploadable() from the database.
 */
class VichUploaderListener implements EventSubscriberInterface {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function postRemoveFile(Event $event) {
        $entity = $event->getObject();

        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            Events::POST_REMOVE => [
                [ 'postRemoveFile', 0]
            ]
        ];
    }


}