<?php

namespace App\Filesystem;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Entity\User;
use App\Http\FlysystemFileResponse;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class MessageFilesystem implements DirectoryNamerInterface {

    private $filesystem;
    private $mimeTypes;
    private $logger;

    public function __construct(FilesystemInterface $filesystem, MimeTypes $mimeTypes, LoggerInterface $logger = null) {
        $this->filesystem = $filesystem;
        $this->mimeTypes = $mimeTypes;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $path
     * @param string $filename
     * @return Response
     * @throws FileNotFoundException
     */
    private function getDownloadResponse(string $path, string $filename): Response {
        if(!$this->filesystem->has($path)) {
            $this->logger->alert('Cannot send document attachment as file does not exist on the filesystem', [
                'file' => $path
            ]);

            throw new FileNotFoundException();
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $mimeType = $this->mimeTypes->getMimeType($extension);

        return new FlysystemFileResponse($this->filesystem, $path, $filename, $mimeType);
    }

    /**
     * @param MessageAttachment $attachment
     * @return Response
     * @throws FileNotFoundException
     */
    public function getMessageAttachmentDownloadResponse(MessageAttachment $attachment): Response {
        $path = $this->getMessageAttachmentPath($attachment);

        return $this->getDownloadResponse($path, $attachment->getFilename());
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
        return sprintf('/%d/%s', $attachment->getMessage()->getId(), $attachment->getPath());
    }

    private function getMessageDirectory(Message $message): string {
        return sprintf('/%d/', $message->getId());
    }

    private function getMessageDownloadsDirectory(Message $message, User $user): string {
        return sprintf('/%d/downloads/%s', $message->getId(), $user->getUsername());
    }

    private function getMessageUploadsDirectory(Message $message, User $user): string {
        return sprintf('/%d/uploads/%s', $message->getId(), $user->getUsername());
    }

    /**
     * @param MessageAttachment $object
     * @param PropertyMapping $mapping
     * @return string
     */
    public function directoryName($object, PropertyMapping $mapping): string {
        return $this->getMessageDirectory($object->getMessage());
    }

    /**
     * @param Message $message
     * @param User $user
     * @return string[]
     */
    public function getUserDownloads(Message $message, User $user) {
        $path = $this->getMessageDownloadsDirectory($message, $user);
        return $this->filesystem->listContents($path);
    }

    public function getUserUploads(Message $message, User $user) {
        $path = $this->getMessageUploadsDirectory($message, $user);
        return $this->filesystem->listContents($path);
    }

    public function uploadFile(Message $message, User $user, UploadedFile $uploadedFile) {
        $path = sprintf('%s/%s', $this->getMessageUploadsDirectory($message, $user), $uploadedFile->getClientOriginalName());

        if($this->filesystem->has($path)) {
            $this->filesystem->delete($path);
        }

        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $this->filesystem->writeStream($path, $stream);
        fclose($stream);
    }

    public function getMessageUserFileDownloadResponse(Message $message, User $user, string $filename): Response {
        $path = sprintf('%s/%s', $this->getMessageDownloadsDirectory($message, $user), $filename);

        // TODO: fix $filename = "../max.mustermann/foo.pdf"

        return $this->getDownloadResponse($path, $filename);
    }

    public function getMessageUploadedUserFileDownloadResponse(Message $message, User $user, string $filename): Response {
        $path = sprintf('%s/%s', $this->getMessageUploadsDirectory($message, $user), $filename);

        // TODO: fix $filename = "../max.mustermann/foo.pdf"

        return $this->getDownloadResponse($path, $filename);
    }
}