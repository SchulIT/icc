<?php

namespace App\Notification\Email;

use App\Entity\Substitution;
use App\Entity\User;
use App\Event\SubstitutionImportEvent;
use App\Repository\UserRepositoryInterface;
use App\Settings\SubstitutionSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubstitutionStrategy implements EmailStrategyInterface {

    private $settings;
    private $translator;
    private $userRepository;

    public function __construct(SubstitutionSettings $settings, UserRepositoryInterface $userRepository, TranslatorInterface $translator) {
        $this->settings = $settings;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
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
            function(User $user) {
                return $user->getEmail() !== null;
            });
    }

    /**
     * @param Substitution $objective
     * @return string
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