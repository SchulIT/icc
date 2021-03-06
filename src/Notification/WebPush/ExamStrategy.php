<?php

namespace App\Notification\WebPush;

use App\Converter\UserStringConverter;
use App\Event\ExamImportEvent;
use App\Repository\UserWebPushSubscriptionRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExamStrategy implements PushNotificationStrategyInterface {

    private $subscriptionRepository;
    private $translator;
    private $userConverter;
    private $settings;

    public function __construct(UserWebPushSubscriptionRepositoryInterface $subscriptionRepository, TranslatorInterface $translator,
                                UserStringConverter $userConverter, ExamSettings $settings) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->translator = $translator;
        $this->userConverter = $userConverter;
        $this->settings = $settings;
    }

    /**
     * @inheritDoc
     */
    public function getSubscriptions($objective): array {
        return $this->subscriptionRepository->findAllForExam();
    }

    /**
     * @inheritDoc
     */
    public function getTitle($objective): string {
        return $this->translator->trans(
            'exam.title',
            [ ],
            'push'
        );
    }

    /**
     * @inheritDoc
     */
    public function getBody($objective): string {
        return $this->translator->trans(
            'exam.content',
            [ ],
            'push'
        );
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof ExamImportEvent;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return $this->settings->isNotificationsEnabled();
    }
}