<?php

namespace App\Message\Doctrine;

use App\Message\Entity\Message;
use App\Message\Event\MessageCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This listener listens for new messages being created and
 * dispatches an event into the EventDispatcher
 */
#[AsDoctrineListener(event: Events::postPersist)]
class MessagePersistSubscriber {

    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof Message) {
            $this->dispatcher->dispatch(new MessageCreatedEvent($entity, true));
        }
    }
}