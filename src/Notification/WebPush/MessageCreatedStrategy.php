<?php

namespace App\Notification\WebPush;

use App\Converter\UserStringConverter;
use App\Entity\UserWebPushSubscription;
use App\Event\MessageCreatedEvent;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserWebPushSubscriptionRepositoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageCreatedStrategy implements PushNotificationStrategyInterface, PostPushSendActionInterface {

    private $subscriptionRepository;
    private $messageRepository;
    private $translator;
    private $userConverter;
    private $dateHelper;

    public function __construct(UserWebPushSubscriptionRepositoryInterface $subscriptionRepository, MessageRepositoryInterface $messageRepository,
                                TranslatorInterface $translator, UserStringConverter $userConverter, DateHelper $dateHelper) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->messageRepository = $messageRepository;
        $this->translator = $translator;
        $this->userConverter = $userConverter;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return UserWebPushSubscription[]
     */
    public function getSubscriptions($objective): array {
        if($objective->getMessage()->isEmailNotificationSent() || $objective->getMessage()->getStartDate() > $this->dateHelper->getToday()) {
            return [ ];
        }

        return $this->subscriptionRepository->findAllForMessage($objective->getMessage());
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string
     */
    public function getTitle($objective): string {
        return $this->translator->trans(
            'message.create.title',
            [
                '%title%' => $objective->getMessage()->getTitle()
            ],
            'push'
        );
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string
     */
    public function getBody($objective): string {
        return $this->translator->trans(
            'message.create.content',
            [
                '%author%' => $this->userConverter->convert($objective->getMessage()->getCreatedBy())
            ],
            'push'
        );
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof MessageCreatedEvent;
    }

    /**
     * @param MessageCreatedEvent $objective
     */
    public function onNotificationSent($objective): void {
        $objective->getMessage()->setIsPushNotificationSent(true);
        $this->messageRepository->persist($objective->getMessage());
    }
}