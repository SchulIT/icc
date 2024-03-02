<?php

namespace App\Filesystem;

use App\Entity\ChatMessageAttachment;
use App\Http\FlysystemFileResponse;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Mimey\MimeTypes;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class ChatFilesystem implements DirectoryNamerInterface {

    public function __construct(private readonly FilesystemOperator $filesystem, private readonly MimeTypes $mimeTypes, private readonly LoggerInterface $logger) {

    }

    public function getDownloadResponse(ChatMessageAttachment $attachment): Response {
        $path = $this->getPath($attachment);

        if(!$this->filesystem->fileExists($path)) {
            $this->logger->alert(
                sprintf(
                    'Datei %s kann nicht gesendet werden (UUID: %s, Pfad: %s). Datei nicht vorhanden',
                    $attachment->getPath(),
                    $attachment->getUuid(),
                    $path
                )
            );

            throw new FileNotFoundException();
        }

        $extension = pathinfo($attachment->getFilename(), PATHINFO_EXTENSION);
        $mimeType = $this->mimeTypes->getMimeType($extension);

        return new FlysystemFileResponse($this->filesystem, $this->getPath($attachment), $attachment->getFilename(), $mimeType);
    }

    public function removeAttachment(ChatMessageAttachment $attachment): void {
        $path = $this->getPath($attachment);

        try {
            $this->filesystem->delete($path);
        } catch (FilesystemException $exception) {
            $this->logger->notice(
                sprintf(
                    'Löschen von %s (UUID: %s, Pfad: %s) nicht möglich: %s',
                    $attachment->getPath(),
                    $attachment->getUuid(),
                    $path,
                    $exception->getMessage()
                ),
                [
                    'exception' => $exception
                ]
            );
        }
    }

    private function getPath(ChatMessageAttachment $attachment): string {
        return sprintf('%s/%s', $attachment->getMessage()->getChat()->getUuid(), $attachment->getPath());
    }

    /**
     * @param ChatMessageAttachment $object
     * @param PropertyMapping $mapping
     * @return string
     */
    public function directoryName(object|array $object, PropertyMapping $mapping): string {
        return sprintf('/%s/', $object->getMessage()->getChat()->getUuid());
    }
}