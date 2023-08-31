<?php

namespace App\Doctrine;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Filesystem\MessageFilesystem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;

/**
 * Listens to Message/MessageAttachment removals in order to remove files and directories from the filesystem.
 */
#[AsDoctrineListener(event: Events::postRemove)]
class MessageRemoveSubscriber {

    public function __construct(private readonly MessageFilesystem $filesystem)
    {
    }

    public function postRemove(PostRemoveEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof Message) {
            $this->filesystem->removeMessageDirectoy($entity);
        } else if($entity instanceof MessageAttachment) {
            $this->filesystem->removeMessageAttachment($entity);
        }
    }
}