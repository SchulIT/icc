<?php

namespace App\Message\Twig;

use App\Common\Entity\User;
use App\Message\Entity\Message;
use App\Message\Entity\MessageFileUpload;
use App\Message\Filesystem\MessageFilesystem;
use App\Message\MessageFileUploadHelper;
use App\Message\Voter\MessageVoter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FilesystemExtension extends AbstractExtension {

    public function __construct(
        private MessageFilesystem $messageFilesystem,
        private MessageFileUploadHelper $messageFileUploadHelper,
        private TokenStorageInterface $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) { }

    public function getFunctions(): array {
        return [
            new TwigFunction('user_upload_exists', [ $this, 'userUploadExists']),
            new TwigFunction('missing_uploads', [ $this, 'getMissingUploads' ]),
            new TwigFunction('message_downloads', [ $this, 'messageDownloads' ]),
        ];
    }

    public function userUploadExists(MessageFileUpload $fileUpload): bool {
        return $this->messageFilesystem->messageUploadedUserFileExists($fileUpload, $fileUpload->getUser());
    }

    public function getMissingUploads(Message $message) {
        if($this->authorizationChecker->isGranted(MessageVoter::Upload, $message) !== true) {
            return [ ];
        }

        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return true;
        }

        $user = $token->getUser();

        if($user instanceof User) {
            return $this->messageFileUploadHelper->getMissingUploadedFiles($message, $user);
        }

        return false;
    }

    public function messageDownloads(Message $message): array {
        if($this->authorizationChecker->isGranted(MessageVoter::Download, $message) !== true) {
            return [ ];
        }

        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return [ ];
        }

        $user = $token->getUser();

        if($user instanceof User) {
            return $this->messageFilesystem->getUserDownloads($message, $user);
        }

        return [ ];
    }
}