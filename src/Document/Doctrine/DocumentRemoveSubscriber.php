<?php

namespace App\Document\Doctrine;

use App\Document\Entity\Document;
use App\Document\Entity\DocumentAttachment;
use App\Document\Filesystem\DocumentFilesystem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;

/**
 * Listens to Document/DocumentAttachment removals in order to remove the files and directories from the filesystem.
 */
#[AsDoctrineListener(event: Events::postRemove)]
class DocumentRemoveSubscriber {

    public function __construct(private readonly DocumentFilesystem $filesystem)
    {
    }

    public function postRemove(PostRemoveEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof Document) {
            $this->filesystem->removeDocumentDirectory($entity);
        } else if($entity instanceof DocumentAttachment) {
            $this->filesystem->removeDocumentAttachment($entity);
        }
    }
}