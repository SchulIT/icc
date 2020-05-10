<?php

namespace App\Doctrine;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Filesystem\MessageFilesystem;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Listens to Message/MessageAttachment removals in order to remove files and directories from the filesystem.
 */
class MessageRemoveSubscriber implements EventSubscriber {

    private $filesystem;

    public function __construct(MessageFilesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    public function postRemove(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();

        if($entity instanceof Message) {
            $this->filesystem->removeMessageDirectoy($entity);
        } else if($entity instanceof MessageAttachment) {
            $this->filesystem->removeMessageAttachment($entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents() {
        return [
            Events::postRemove
        ];
    }
}