<?php

namespace App\Filesystem;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Entity\MessageFileUpload;
use App\Entity\User;
use App\Exception\UnexpectedTypeException;
use App\Http\FlysystemFileResponse;
use Exception;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Mimey\MimeTypes;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class MessageFilesystem implements DirectoryNamerInterface {

    private TokenStorageInterface $tokenStorage;
    private FilesystemOperator $filesystem;
    private MimeTypes $mimeTypes;
    private LoggerInterface|NullLogger $logger;

    public function __construct(TokenStorageInterface $tokenStorage, FilesystemOperator $filesystem, MimeTypes $mimeTypes, LoggerInterface $logger = null) {
        $this->tokenStorage = $tokenStorage;
        $this->filesystem = $filesystem;
        $this->mimeTypes = $mimeTypes;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $path
     * @param string $filename
     * @return Response
     * @throws FileNotFoundException|FilesystemException
     */
    private function getDownloadResponse(string $path, string $filename): Response {
        if(!$this->filesystem->fileExists($path)) {
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
     * @throws FilesystemException
     */
    public function getMessageAttachmentDownloadResponse(MessageAttachment $attachment): Response {
        $path = $this->getMessageAttachmentPath($attachment);

        return $this->getDownloadResponse($path, $attachment->getFilename());
    }

    /**
     * @throws FilesystemException
     */
    public function removeMessageAttachment(MessageAttachment $attachment): void {
        $path = $this->getMessageAttachmentPath($attachment);

        if($attachment->getMessage() !== null && $this->filesystem->fileExists($path)) {
            $this->filesystem->delete($path);
        }
    }

    /**
     * @throws FilesystemException
     */
    public function removeMessageDirectoy(Message $message): void {
        $path = $this->getMessageDirectory($message);

        if($this->filesystem->directoryExists($path)) {
            $this->filesystem->deleteDirectory($path);
        }
    }

    /**
     * @throws FilesystemException
     */
    public function removeUserFileDownload(Message $message, User $user, string $filename): void {
        $path = sprintf('%s/%s', $this->getMessageDownloadsDirectory($message, $user), $filename);

        if($this->filesystem->fileExists($path)) {
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
    public function getMessageUploadsDirectory(MessageFileUpload|Message $messageOrUpload, ?User $user): string {
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

            if($user instanceof User) {
                return $this->getMessageUploadsDirectory($object, $user);
            }

            throw new RuntimeException('User must not be null.');
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
     * @throws FilesystemException
     */
    public function getUserDownloads(Message $message, User $user): array {
        $path = $this->getMessageDownloadsDirectory($message, $user);
        return $this->filesystem
            ->listContents($path)
            ->map(fn(StorageAttributes $attributes) => [
                'basename' => basename($attributes->path())
            ])
            ->toArray();
    }

    /**
     * Returns information about a given user specific download
     *
     * @param Message $message
     * @param User $user
     * @param string $filename
     * @return array|null
     */
    public function getUserDownload(Message $message, User $user, string $filename): ?array {
        $path = sprintf('%s/%s',
            $this->getMessageDownloadsDirectory($message, $user),
            $filename
        );

        try {
            return [
                'basename' => basename($filename),
                'size' => $this->filesystem->fileSize($path),
                'timestamp' => $this->filesystem->lastModified($path)
            ];
        } catch (Exception | FilesystemException) {
            return null;
        }
    }

    /**
     * Returns all user downloads as nested array
     *
     * @param Message $message
     * @return array
     * @throws FilesystemException
     */
    public function getAllUserDownloads(Message $message): array {
        $path = $this->getMessageDownloadsDirectory($message, null);

        $folders = $this->filesystem->listContents($path, true)->filter(fn(StorageAttributes $attributes) => $attributes->isDir());
        $files = $this->filesystem->listContents($path, true)->filter(fn(StorageAttributes $attributes) => $attributes->isFile());

        $structure = [ ];

        /** @var StorageAttributes $folder */
        foreach($folders as $folder) {
            $structure[basename($folder->path())] = [
                'path' => $folder->path(),
                'basename' => basename($folder->path()),
                'files' => [ ]
            ];
        }

        /** @var FileAttributes $file */
        foreach($files as $file) {
            $structure[basename(dirname($file->path()))]['files'][] = [
                'basename' => basename($file->path()),
                'size' => $file->fileSize(),
                'timestamp' => $file->lastModified()
            ];
        }

        return $structure;
    }


    /**
     * Uploads a user-specific download
     *
     * @param Message $message
     * @param User $user
     * @param UploadedFile $uploadedFile
     * @throws FilesystemException
     */
    public function uploadUserDownload(Message $message, User $user, UploadedFile $uploadedFile) {
        $path = sprintf('%s/%s', $this->getMessageDownloadsDirectory($message, $user), $uploadedFile->getClientOriginalName());

        if($this->filesystem->fileExists($path)) {
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
     * @throws FilesystemException
     */
    public function uploadFile(Message $message, User $user, UploadedFile $uploadedFile) {
        $path = sprintf('%s/%s', $this->getMessageUploadsDirectory($message, $user), $uploadedFile->getClientOriginalName());

        if($this->filesystem->fileExists($path)) {
            $this->filesystem->delete($path);
        }

        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $this->filesystem->writeStream($path, $stream);
        fclose($stream);
    }

    /**
     * @throws FileNotFoundException|FilesystemException
     */
    public function getMessageUserFileDownloadResponse(Message $message, User $user, string $filename): Response {
        $path = sprintf('%s/%s', $this->getMessageDownloadsDirectory($message, $user), $filename);

        if(str_contains($path, '/../')) {
            throw new FileNotFoundException();
        }

        return $this->getDownloadResponse($path, $filename);
    }

    /**
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    public function getMessageUploadedUserFileDownloadResponse(MessageFileUpload $fileUpload, User $user): Response {
        $path = sprintf('%s/%s', $this->getMessageUploadsDirectory($fileUpload, $user), $fileUpload->getPath());
        return $this->getDownloadResponse($path, $fileUpload->getFilename());
    }

    /**
     * @throws FilesystemException
     */
    public function messageUploadedUserFileExists(MessageFileUpload $fileUpload, User $user): bool {
        $path = sprintf('%s/%s', $this->getMessageUploadsDirectory($fileUpload, $user), $fileUpload->getPath());

        return $this->filesystem->fileExists($path);
    }
}