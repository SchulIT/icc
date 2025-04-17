<?php

namespace App\Doctrine;

use App\Entity\ReturnItem;
use App\Event\ReturnItemCreatedEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist)]
readonly class ReturnItemPersistSubscriber {

    public function __construct(private DoctrineEventsCollector $collector) {

    }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $object = $eventArgs->getObject();

        if($object instanceof ReturnItem) {
            $this->collector->collect(new ReturnItemCreatedEvent($object));
        }
    }
}