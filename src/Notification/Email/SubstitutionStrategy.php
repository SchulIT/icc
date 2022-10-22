<?php

namespace App\Notification\Email;

use App\Entity\Substitution;
use App\Entity\User;
use App\Event\SubstitutionImportEvent;
use App\Repository\UserRepositoryInterface;
use App\Settings\SubstitutionSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubstitutionStrategy implements EmailStrategyInterface {

    public function __construct(private SubstitutionSettings $settings, private UserRepositoryInterface $userRepository, private TranslatorInterface $translator)
    {
    }

    /**
     * @inheritDoc
     */
    public function getReplyTo($objective): ?string {
        return $this->settings->getNotificationReplyToAddress();
    }

    /**
     * @inheritDoc
     */
    public function getRecipients($objective): array {
        return array_filter(
            $this->userRepository->findAllByNotifySubstitutions(),
            fn(User $user) => $user->getEmail() !== null && $user->isEmailNotificationsEnabled());
    }

    /**
     * @param Substitution $objective
     */
    public function getSender($objective): string {
        return $this->settings->getNotificationSender();
    }

    /**
     * @inheritDoc
     */
    public function getSubject($objective): string {
        return $this->translator->trans('substitution.title', [], 'email');
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string {
        return 'email/substitution.html.twig';
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return $this->settings->isNotificationsEnabled();
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof SubstitutionImportEvent;
    }
}