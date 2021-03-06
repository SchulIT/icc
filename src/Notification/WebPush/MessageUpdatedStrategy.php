<?php

namespace App\Notification\WebPush;

use App\Converter\UserStringConverter;
use App\Entity\UserWebPushSubscription;
use App\Event\MessageUpdatedEvent;
use App\Message\MessageRecipientResolver;
use App\Repository\UserWebPushSubscriptionRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageUpdatedStrategy implements PushNotificationStrategyInterface {

    private $subscriptionRepository;
    private $translator;
    private $userConverter;
    private $dateHelper;
    private $recipientResolver;

    public function __construct(UserWebPushSubscriptionRepositoryInterface $subscriptionRepository, TranslatorInterface $translator,
                                MessageRecipientResolver $recipientResolver, UserStringConverter $userConverter, DateHelper $dateHelper) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->translator = $translator;
        $this->userConverter = $userConverter;
        $this->dateHelper = $dateHelper;
        $this->recipientResolver = $recipientResolver;
    }

    /**
     * @param MessageUpdatedEvent $objective
     * @return UserWebPushSubscription[]
     */
    public function getSubscriptions($objective): array {
        if($objective->getMessage()->getStartDate() > $this->dateHelper->getToday()) {
            return [ ];
        }

        return $this->subscriptionRepository->findAllForUsers(
            $this->recipientResolver->resolveRecipients($objective->getMessage())
        );
    }

    /**
     * @param MessageUpdatedEvent $objective
     * @return string
     */
    public function getTitle($objective): string {
        return $this->translator->trans(
            'message.update.title',
            [
                '%title%' => $objective->getMessage()->getTitle()
            ],
            'push'
        );
    }

    /**
     * @param MessageUpdatedEvent $objective
     * @return string
     */
    public function getBody($objective): string {
        return $this->translator->trans(
            'message.update.content',
            [
                '%author%' => $this->userConverter->convert($objective->getMessage()->getUpdatedBy())
            ],
            'push'
        );
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof MessageUpdatedEvent;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return true;
    }
}