<?php

namespace App\Doctrine;

use App\Entity\Message;
use App\Entity\MessagePollVote;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class MessagePollChoiceSubscriber implements EventSubscriber {

    public function postUpdate(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();

        if($entity instanceof Message) {
            foreach($uow->getScheduledCollectionUpdates() as $collection) {
                if($collection === $entity->getPollChoices()) {
                    // TODO
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array {
        return [
            Events::postUpdate
        ];
    }
}