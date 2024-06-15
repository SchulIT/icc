<?php

namespace App\Doctrine;

use App\Entity\Chat;
use App\Filesystem\ChatFilesystem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postRemove)]
class ChatRemoveSubscriber {
    public function __construct(private readonly ChatFilesystem $chatFilesystem) {

    }

    public function postRemove(PostRemoveEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof Chat) {
            $this->chatFilesystem->removeChatDirectory($entity);
        }
    }
}