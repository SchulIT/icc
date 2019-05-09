<?php

namespace App\Listener;

use App\Entity\DocumentAttachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

class DocumentAttachmentListener implements EventSubscriberInterface {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function postRemove(Event $event) {
        $object = $event->getObject();

        $this->em->remove($object);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            Events::POST_REMOVE => [ 'postRemove' ]
        ];
    }
}