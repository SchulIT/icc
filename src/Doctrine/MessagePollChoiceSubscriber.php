<?php

namespace App\Doctrine;

use App\Entity\Message;
use App\Entity\MessagePollVote;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

#[AsDoctrineListener(event: Events::postUpdate)]
class MessagePollChoiceSubscriber {

    public function postUpdate(PostUpdateEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();
        $uow = $eventArgs->getObjectManager()->getUnitOfWork();

        if($entity instanceof Message) {
            foreach($uow->getScheduledCollectionUpdates() as $collection) {
                if($collection === $entity->getPollChoices()) {
                    // TODO
                }
            }
        }
    }
}