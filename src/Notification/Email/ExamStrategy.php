<?php

namespace App\Notification\Email;

use App\Entity\User;
use App\Event\ExamImportEvent;
use App\Repository\UserRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExamStrategy implements EmailStrategyInterface {

    private $settings;
    private $translator;
    private $userRepository;

    public function __construct(ExamSettings $settings, TranslatorInterface $translator, UserRepositoryInterface $userRepository) {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->userRepository = $userRepository;
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
    public function getReplyTo($objective): ?string {
        return $this->settings->getNotificationReplyToAddress();
    }

    /**
     * @inheritDoc
     */
    public function getRecipients($objective): array {
        return array_filter(
            $this->userRepository->findAllByNotifyExams(),
            function(User $user) {
                return $user->getEmail() !== null && $user->isEmailNotificationsEnabled();
            });
    }

    /**
     * @inheritDoc
     */
    public function getSubject($objective): string {
        return $this->translator->trans('exam.title', [], 'email');
    }

    /**
     * @inheritDoc
     */
    public function getSender($objective): string {
        return $this->settings->getNotificationSender();
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string {
        return 'email/exam.html.twig';
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof ExamImportEvent;
    }
}