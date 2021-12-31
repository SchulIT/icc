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
use App\View\Filter\FilterViewInterface;
use DateInterval;
use DateTime;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HelperExtension extends AbstractExtension {

    private MessageConfirmationHelper $confirmationHelper;
    private DismissedMessagesHelper $dismissedHelper;
    private RefererHelper $redirectHelper;
    private ColorUtils $colorUtils;
    private MessageFilesystem $messageFilesystem;
    private MessageFileUploadHelper $messageFileUploadHelper;
    private TokenStorageInterface $tokenStorage;
    private AuthorizationCheckerInterface $authorizationChecker;
    private ValidatorInterface $validator;

    public function __construct(MessageConfirmationHelper $confirmationHelper, DismissedMessagesHelper $dismissedHelper,
                                RefererHelper $redirectHelper, ColorUtils $colorUtils, MessageFilesystem $messageFilesystem,
                                MessageFileUploadHelper $messageFileUploadHelper, TokenStorageInterface $tokenStorage,
                                AuthorizationCheckerInterface $authorizationChecker, ValidatorInterface $validator) {
        $this->confirmationHelper = $confirmationHelper;
        $this->dismissedHelper = $dismissedHelper;
        $this->redirectHelper = $redirectHelper;
        $this->colorUtils = $colorUtils;
        $this->messageFilesystem = $messageFilesystem;
        $this->messageFileUploadHelper = $messageFileUploadHelper;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->validator = $validator;
    }

    public function getFilters(): array {
        return [
            new TwigFilter('previous_date', [ $this, 'getPreviousDate' ]),
            new TwigFilter('next_date', [ $this, 'getNextDate']),
            new TwigFilter('clone', [ $this, 'cloneObject' ])
        ];
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('is_confirmed', [ $this, 'isConfirmed' ]),
            new TwigFunction('is_dismissed', [ $this, 'isDismissed' ]),
            new TwigFunction('missing_uploads', [ $this, 'getMissingUploads' ]),
            new TwigFunction('message_downloads', [ $this, 'messageDownloads' ]),
            new TwigFunction('referer_path', [ $this, 'refererPath' ]),
            new TwigFunction('foreground', [ $this, 'foregroundColor' ]),
            new TwigFunction('validation_errors', [ $this, 'validate' ]),
            new TwigFunction('contains_active_filters', [ $this, 'containsActiveFilters']),
            new TwigFunction('is_in_datetime_array', [ $this, 'isInDateTimeArray'])
        ];
    }

    public function getPreviousDate(\DateTime $dateTime, bool $skipWeekends = false): DateTime {
        $previous = (clone $dateTime)->sub(new DateInterval('P1D'));

        while($skipWeekends === true && $previous->format('N') > 5) {
            $previous->modify('-1 day');
        }

        return $previous;
    }

    public function getNextDate(\DateTime $dateTime, bool $skipWeekends = false): DateTime {
        $next = (clone $dateTime)->add(new DateInterval('P1D'));

        while($skipWeekends === true && $next->format('N') > 5) {
            $next->modify('+1 day');
        }

        return $next;
    }

    public function cloneObject(object $object): object {
        return clone $object;
    }

    public function isInDateTimeArray(\DateTime $dateTime, array $dateTimes): bool {
        foreach($dateTimes as $item) {
            if($item == $dateTime) {
                return true;
            }
        }

        return false;
    }

    public function isConfirmed(Message $message): bool {
        return $this->confirmationHelper->isMessageConfirmed($message);
    }

    public function isDismissed(Message $message): bool {
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

    public function refererPath(array $mapping, string $route, array $parameters = [ ]): string {
        return $this->redirectHelper->getRefererPathFromQuery($mapping, $route, $parameters);
    }

    public function foregroundColor(string $color): string {
        return $this->colorUtils->getForeground($color);
    }

    public function validate($object): ConstraintViolationListInterface {
        return $this->validator->validate($object);
    }

    /**
     * @param FilterViewInterface[] $filters
     * @return bool
     */
    public function containsActiveFilters(array $filters): bool {
        foreach($filters as $filter) {
            if($filter->isEnabled()) {
                return true;
            }
        }

        return false;
    }
}