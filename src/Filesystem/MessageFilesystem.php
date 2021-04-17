<?php

namespace App\Filesystem;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Entity\MessageFileUpload;
use App\Entity\User;
use App\Exception\UnexpectedTypeException;
use App\Http\FlysystemFileResponse;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class MessageFilesystem implements DirectoryNamerInterface {

    private $tokenStorage;
    private $filesystem;
    private $mimeTypes;
    private $logger;

    public function __construct(TokenStorageInterface $tokenStorage, FilesystemInterface $filesystem, MimeTypes $mimeTypes, LoggerInterface $logger = null) {
        $this->tokenStorage = $tokenStorage;
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

        if($attachment->getMessage() !== null && $this->filesystem->has($path)) {
            $this->filesystem->delete($path);
        }
    }

    public function removeMessageDirectoy(Message $message): void {
        $path = $this->getMessageDirectory($message);

        if($this->filesystem->has($path)) {
            $this->filesystem->deleteDir($path);
        }
    }

    public function removeUserFileDownload(Message $message, User $user, string $filename): void {
        $path = sprintf('%s/%s', $this->getMessageDownloadsDirectory($message, $user), $filename);

        if($this->filesystem->has($path)) {
            $this->filesystem->delete($path);
        }
    }

    /**
     * Returns the full path of a MessageAttachment
     *
     * @param MessageAttachment $attachment
     * @return string
     */
    private function getMessageAttachmentPath(MessageAttachment $attachment): string {
        return sprintf('/%s/%s', $attachment->getMessage()->getUuid(), $attachment->getPath());
    }


    /**
     * Returns the messages basedir which is used for any sorts of file (attachments and user specific downloads/uploads)
     *
     * @param Message $message
     * @return string
     */
    private function getMessageDirectory(Message $message): string {
        return sprintf('/%s/', $message->getUuid());
    }

    /**
     * Returns the base dir for user specific downloads
     *
     * @param Message $message
     * @param User|null $user The directory of a specific user (if specified)
     * @return string
     */
    public function getMessageDownloadsDirectory(Message $message, ?User $user): string {
        if($user === null) {
            return sprintf('/%s/downloads', $message->getUuid());
        }

        return sprintf('/%s/downloads/%s', $message->getUuid(), $user->getUsername());
    }

    /**
     * Returns the directory for user specific uploads
     *
     * @param Message|MessageFileUpload $messageOrUpload
     * @param User|null $user The directory of a specific user (if specified)
     * @return string
     */
    public function getMessageUploadsDirectory($messageOrUpload, ?User $user): string {
        if($user === null) {
            return sprintf('/%s/uploads', $messageOrUpload->getUuid());
        }

        return sprintf('/%s/uploads/%s', $messageOrUpload->getMessageFile()->getMessage()->getUuid(), $user->getUsername());
    }

    /**
     * @param MessageAttachment|MessageFileUpload|Message $object
     * @param PropertyMapping $mapping
     * @return string
     * @throws UnexpectedTypeException
     */
    public function directoryName($object, PropertyMapping $mapping): string {
        if($object instanceof MessageFileUpload) {
            $user = $this->tokenStorage->getToken()->getUser();

            if($user instanceof User && $user !== null) {
                return $this->getMessageUploadsDirectory($object, $user);
            }

            throw new \RuntimeException('User must not be null.');
        } else if($object instanceof MessageAttachment) {
            return $this->getMessageDirectory($object->getMessage());
        }

        throw new UnexpectedTypeException($object, [ MessageFileUpload::class, Message::class, MessageAttachment::class ]);
    }

    /**
     * Returns a list of user specific downloads
     *
     * @param Message $message
     * @param User $user
     * @return string[]
     */
    public function getUserDownloads(Message $message, User $user) {
        $path = $this->getMessageDownloadsDirectory($message, $user);
        return $this->filesystem->listContents($path);
    }

    /**
     * Returns information about a given user specific download
     *
     * @param Message $message
     * @param User $user
     * @param string $filename
     * @return string[]|null
     */
    public function getUserDownload(Message $message, User $user, string $filename) {
        $path = sprintf('%s/%s',
            $this->getMessageDownloadsDirectory($message, $user),
            $filename
        );

        try {
            return $this->filesystem->getMetadata($path);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the list of uploaded files of a user
     *
     * @param Message $message
     * @param User $user
     * @return array
     */
    public function getUserUploads(Message $message, User $user) {
        $path = $this->getMessageUploadsDirectory($message, $user);
        return $this->filesystem->listContents($path);
    }

    /**
     * Returns all user downloads as nested array
     *
     * @param Message $message
     * @return array
     */
    public function getAllUserDownloads(Message $message) {
        $path = $this->getMessageDownloadsDirectory($message, null);
        $contents = $this->filesystem->listContents($path, true);

        return $this->makeStructure($contents);
    }

    /**
     * Helper which creates a nested array structure
     *
     * @param array $contents
     * @return array
     */
    private function makeStructure(array $contents) {
        $structure = [ ];

        $folders = array_filter($contents, function(array $item) {
            return $item['type'] === 'dir';
        });
        $files = array_filter($contents, function(array $item) {
            return $item['type'] === 'file';
        });

        foreach($folders as $folder) {
            $structure[$folder['path']] = $folder;
            $structure[$folder['path']]['files'] = [ ];
        }

        foreach($files as $file) {
            $structure[$file['dirname']]['files'][] = $file;
        }

        return $structure;
    }

    /**
     * Uploads a user-specific download
     *
     * @param Message $message
     * @param User $user
     * @param UploadedFile $uploadedFile
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function uploadUserDownload(Message $message, User $user, UploadedFile $uploadedFile) {
        $path = sprintf('%s/%s', $this->getMessageDownloadsDirectory($message, $user), $uploadedFile->getClientOriginalName());

        if($this->filesystem->has($path)) {
            $this->filesystem->delete($path);
        }

        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $this->filesystem->writeStream($path, $stream);
        fclose($stream);
    }

    /**
     * Uploads a user-specific file
     *
     * @param Message $message
     * @param User $user
     * @param UploadedFile $uploadedFile
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
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

    public function getMessageUploadedUserFileDownloadResponse(MessageFileUpload $fileUpload, User $user): Response {
        $path = sprintf('%s/%s', $this->getMessageUploadsDirectory($fileUpload, $user), $fileUpload->getPath());
        return $this->getDownloadResponse($path, $fileUpload->getFilename());
    }
}