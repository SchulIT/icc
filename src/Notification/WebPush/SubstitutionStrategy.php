<?php

namespace App\Notification\WebPush;

use App\Converter\UserStringConverter;
use App\Event\SubstitutionImportEvent;
use App\Repository\UserWebPushSubscriptionRepositoryInterface;
use App\Settings\SubstitutionSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubstitutionStrategy implements PushNotificationStrategyInterface {

    private $subscriptionRepository;
    private $translator;
    private $userConverter;
    private $settings;

    public function __construct(UserWebPushSubscriptionRepositoryInterface $subscriptionRepository, TranslatorInterface $translator,
                                UserStringConverter $userConverter, SubstitutionSettings $settings) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->translator = $translator;
        $this->userConverter = $userConverter;
        $this->settings = $settings;
    }

    /**
     * @inheritDoc
     */
    public function getSubscriptions($objective): array {
        return $this->subscriptionRepository->findAllForSubstitutions();
    }

    /**
     * @inheritDoc
     */
    public function getTitle($objective): string {
        return $this->translator->trans(
            'substitutions.title',
            [ ],
            'push'
        );
    }

    /**
     * @inheritDoc
     */
    public function getBody($objective): string {
        return $this->translator->trans(
            'substitutions.content',
            [ ],
            'push'
        );
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof SubstitutionImportEvent;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return $this->settings->isNotificationsEnabled();
    }
}