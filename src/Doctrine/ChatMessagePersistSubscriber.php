<?php

namespace App\Doctrine;

use App\Entity\ChatMessage;
use App\Event\ChatMessageCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsDoctrineListener(event: Events::postPersist)]
class ChatMessagePersistSubscriber {
    public function __construct(private readonly EventDispatcherInterface $dispatcher) {

    }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof ChatMessage) {
            $this->dispatcher->dispatch(new ChatMessageCreatedEvent($entity));
        }
    }
}