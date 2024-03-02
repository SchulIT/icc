<?php

namespace App\Filesystem;

use App\Entity\Document;
use App\Entity\DocumentAttachment;
use App\Http\FlysystemFileResponse;
use League\Flysystem\FilesystemOperator;
use Mimey\MimeTypes;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DocumentFilesystem implements DirectoryNamerInterface {

    private LoggerInterface|NullLogger $logger;

    public function __construct(private FilesystemOperator $filesystem, private MimeTypes $mimeTypes, LoggerInterface $logger = null) {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws FileNotFoundException
     */
    public function getDownloadResponse(DocumentAttachment $attachment): Response {
        $path = $this->getAttachmentPath($attachment);

        if(!$this->filesystem->fileExists($path)) {
            $this->logger->alert('Cannot send document attachment as file does not exist on the filesystem', [
                'file' => $path
            ]);

            throw new FileNotFoundException();
        }

        $extension = pathinfo($attachment->getFilename(), PATHINFO_EXTENSION);
        $mimeType = $this->mimeTypes->getMimeType($extension);

        return new FlysystemFileResponse($this->filesystem, $this->getAttachmentPath($attachment), $attachment->getFilename(), $mimeType);
    }

    public function removeDocumentAttachment(DocumentAttachment $attachment): void {
        $path = $this->getAttachmentPath($attachment);

        if($attachment->getDocument() !== null && $this->filesystem->fileExists($path)) {
            $this->filesystem->delete($this->getAttachmentPath($attachment));
        }
    }

    public function removeDocumentDirectory(Document $document): void {
        $path = $this->getAttachmentsDirectory($document);

        if($this->filesystem->directoryExists($path)) {
            $this->filesystem->deleteDirectory($path);
        }
    }

    public function getAttachmentsDirectory(Document $document): string {
        return sprintf('/%s/', $document->getUuid());
    }

    public function getAttachmentPath(DocumentAttachment $attachment): string {
        return sprintf('/%s/%s', $attachment->getDocument()->getUuid(), $attachment->getPath());
    }

    /**
     * @param DocumentAttachment $object
     */
    public function directoryName($object, PropertyMapping $mapping): string {
        return $this->getAttachmentsDirectory($object->getDocument());
    }
}