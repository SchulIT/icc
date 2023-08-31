<?php

namespace App\Doctrine;

use App\Entity\BookComment;
use App\Event\BookCommentCreatedEvent;
use App\Event\BookCommentUpdatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
class StudentCommentSubscriber {

    public function __construct(private readonly EventDispatcherInterface $dispatcher) { }

    public function postPersist(PostPersistEventArgs $event): void {
        $entity = $event->getObject();

        if($entity instanceof BookComment) {
            $this->dispatcher->dispatch(new BookCommentCreatedEvent($entity));
        }
    }

    public function postUpdate(PostUpdateEventArgs $event): void {
        $entity = $event->getObject();

        if($entity instanceof BookComment) {
            $this->dispatcher->dispatch(new BookCommentUpdatedEvent($entity));
        }
    }
}