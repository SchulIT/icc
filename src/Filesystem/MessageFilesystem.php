<?php

namespace App\Filesystem;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Http\FlysystemFileResponse;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class MessageFilesystem implements DirectoryNamerInterface {

    private $filesystem;
    private $logger;

    public function __construct(FilesystemInterface $filesystem, LoggerInterface $logger = null) {
        $this->filesystem = $filesystem;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param MessageAttachment $attachment
     * @return Response
     * @throws FileNotFoundException
     */
    public function getMessageAttachmentDownloadResponse(MessageAttachment $attachment): Response {
        $path = $this->getMessageAttachmentPath($attachment);

        if(!$this->filesystem->has($path)) {
            $this->logger->alert('Cannot send document attachment as file does not exist on the filesystem', [
                'file' => $path
            ]);

            throw new FileNotFoundException();
        }

        return new FlysystemFileResponse($this->filesystem, $this->getMessageAttachmentPath($attachment), $attachment->getFilename());
    }

    public function removeMessageAttachment(MessageAttachment $attachment): void {
        $path = $this->getMessageAttachmentPath($attachment);

        if($attachment->getId() !== null && $attachment->getMessage() !== null && $attachment->getMessage()->getId() !== null && $this->filesystem->has($path)) {
            $this->filesystem->delete($path);
        }
    }

    public function removeMessageDirectoy(Message $message): void {
        if($message->getId() !== null) {
            $this->filesystem->deleteDir($this->getMessageDirectory($message));
        }
    }

    private function getMessageAttachmentPath(MessageAttachment $attachment): string {
        return sprintf('/%d/%s', $attachment->getMessage()->getId(), $attachment->getFilename());
    }

    private function getMessageDirectory(Message $message): string {
        return sprintf('/%d/', $message->getId());
    }

    /**
     * @param MessageAttachment $object
     * @param PropertyMapping $mapping
     * @return string
     */
    public function directoryName($object, PropertyMapping $mapping): string {
        return $this->getMessageDirectory($object->getMessage());
    }
}