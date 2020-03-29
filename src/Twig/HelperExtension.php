<?php

namespace App\Twig;

use App\Entity\Message;
use App\Entity\User;
use App\Filesystem\MessageFilesystem;
use App\Message\DismissedMessagesHelper;
use App\Message\MessageConfirmationHelper;
use App\Message\MessageFileUploadHelper;
use App\Security\Voter\MessageVoter;
use App\Utils\ColorUtils;
use App\Utils\RefererHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HelperExtension extends AbstractExtension {

    private $confirmationHelper;
    private $dismissedHelper;
    private $redirectHelper;
    private $colorUtils;
    private $messageFilesystem;
    private $messageFileUploadHelper;
    private $tokenStorage;
    private $authorizationChecker;

    public function __construct(MessageConfirmationHelper $confirmationHelper, DismissedMessagesHelper $dismissedHelper,
                                RefererHelper $redirectHelper, ColorUtils $colorUtils, MessageFilesystem $messageFilesystem,
                                MessageFileUploadHelper $messageFileUploadHelper, TokenStorageInterface $tokenStorage,
                                AuthorizationCheckerInterface $authorizationChecker) {
        $this->confirmationHelper = $confirmationHelper;
        $this->dismissedHelper = $dismissedHelper;
        $this->redirectHelper = $redirectHelper;
        $this->colorUtils = $colorUtils;
        $this->messageFilesystem = $messageFilesystem;
        $this->messageFileUploadHelper = $messageFileUploadHelper;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getFunctions() {
        return [
            new TwigFunction('is_confirmed', [ $this, 'isConfirmed' ]),
            new TwigFunction('is_dismissed', [ $this, 'isDismissed' ]),
            new TwigFunction('missing_uploads', [ $this, 'getMissingUploads' ]),
            new TwigFunction('message_downloads', [ $this, 'messageDownloads' ]),
            new TwigFunction('referer_path', [ $this, 'refererPath' ]),
            new TwigFunction('foreground', [ $this, 'foregroundColor' ])
        ];
    }

    public function isConfirmed(Message $message) {
        return $this->confirmationHelper->isMessageConfirmed($message);
    }

    public function isDismissed(Message $message) {
        return $this->dismissedHelper->isMessageDismissed($message);
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

    public function messageDownloads(Message $message) {
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

    public function refererPath(array $mapping, string $route, array $parameters = [ ]): string {
        return $this->redirectHelper->getRefererPathFromQuery($mapping, $route, $parameters);
    }

    public function foregroundColor(string $color): string {
        return $this->colorUtils->getForeground($color);
    }
}