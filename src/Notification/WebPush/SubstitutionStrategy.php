<?php

namespace App\Notification\WebPush;

use App\Converter\UserStringConverter;
use App\Repository\UserWebPushSubscriptionRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubstitutionStrategy implements PushNotificationStrategyInterface {

    private $subscriptionRepository;
    private $translator;
    private $userConverter;

    public function __construct(UserWebPushSubscriptionRepositoryInterface $subscriptionRepository, TranslatorInterface $translator, UserStringConverter $userConverter) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->translator = $translator;
        $this->userConverter = $userConverter;
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
}