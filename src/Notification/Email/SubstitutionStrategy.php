<?php

namespace App\Notification\Email;

use App\Repository\UserRepositoryInterface;

class SubstitutionStrategy implements EmailStrategyInterface {

    private $replyTo = null;
    private $userRepository;

    public function __construct(?string $replyTo, UserRepositoryInterface $userRepository) {
        $this->replyTo = $replyTo;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function getReplyTo(): ?string {
        return $this->replyTo;
    }

    /**
     * @inheritDoc
     */
    public function getUserEnrolledForNotification() {
        return $this->userRepository->findAllByNotifySubstitutions();
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string {
        return 'notifications.email.substitutions';
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string {
        return 'emails/notifications/substitutions.plain.html.twig';
    }
}