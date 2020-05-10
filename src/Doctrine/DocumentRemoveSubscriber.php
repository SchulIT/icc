<?php

namespace App\Doctrine;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Filesystem\DocumentFilesystem;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Listens to Document/DocumentAttachment removals in order to remove the files and directories from the filesystem.
 */
class DocumentRemoveSubscriber implements EventSubscriber {

    private $filesystem;

    public function __construct(DocumentFilesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    public function postRemove(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();

        if($entity instanceof Document) {
            $this->filesystem->removeDocumentDirectory($entity);
        } else if($entity instanceof DocumentAttachment) {
            $this->filesystem->removeDocumentAttachment($entity);
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