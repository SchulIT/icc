<?php

namespace App\Doctrine;

use App\Entity\ReturnItem;
use App\Event\ReturnItemReturnedEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postUpdate)]
readonly class ReturnItemReturnedSubscriber {

    public function __construct(private DoctrineEventsCollector $collector) {

    }

    public function postUpdate(PostUpdateEventArgs $eventArgs): void {
        $object = $eventArgs->getObject();

        if(!$object instanceof ReturnItem) {
            return;
        }

        $changeset = $eventArgs->getObjectManager()->getUnitOfWork()->getEntityChangeSet($object);

        if(array_key_exists('isReturned', $changeset) && $object->isReturned() === true) {
            $this->collector->collect(new ReturnItemReturnedEvent($object));
        }
    }
}