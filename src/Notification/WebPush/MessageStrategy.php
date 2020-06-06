<?php

namespace App\Notification\WebPush;

use App\Converter\UserStringConverter;
use App\Entity\Message;
use App\Entity\UserWebPushSubscription;
use App\Repository\UserWebPushSubscriptionRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageStrategy implements PushNotificationStrategyInterface {

    private $subscriptionRepository;
    private $translator;
    private $userConverter;

    public function __construct(UserWebPushSubscriptionRepositoryInterface $subscriptionRepository, TranslatorInterface $translator, UserStringConverter $userConverter) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->translator = $translator;
        $this->userConverter = $userConverter;
    }

    /**
     * @param Message $objective
     * @return UserWebPushSubscription[]
     */
    public function getSubscriptions($objective): array {
        return $this->subscriptionRepository->findAllForMessage($objective);
    }

    /**
     * @param Message $objective
     * @return string
     */
    public function getTitle($objective): string {
        return $this->translator->trans(
            'message.title',
            [
                '%title%' => $objective->getTitle()
            ],
            'push'
        );
    }

    /**
     * @param Message $objective
     * @return string
     */
    public function getBody($objective): string {
        return $this->translator->trans(
            'message.content',
            [
                '%author%' => $this->userConverter->convert($objective->getCreatedBy())
            ],
            'push'
        );
    }
}