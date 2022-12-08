<?php

namespace App\Notification\Email;

use App\Entity\MessageScope;
use App\Event\MessageUpdatedEvent;
use App\Message\MessageRecipientResolver;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageUpdatedStrategy implements EmailStrategyInterface, PostEmailSendActionInterface {

    public function __construct(private TranslatorInterface $translator, private MessageRecipientResolver $recipientResolver, private DateHelper $dateHelper, private MessageRepositoryInterface $messageRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return true;
    }

    /**
     * @param MessageUpdatedEvent $objective
     */
    public function getReplyTo($objective): ?string {
        $creator = $objective->getMessage()->getUpdatedBy();

        if($creator === null) {
            return null;
        }

        return $creator->getEmail();
    }

    /**
     * @param MessageUpdatedEvent $objective
     */
    public function getRecipients($objective): array {
        return $this->recipientResolver->resolveRecipients($objective->getMessage());
    }

    /**
     * @param MessageUpdatedEvent $objective
     */
    public function getSubject($objective): string {
        return $this->translator->trans('message.update.title', ['%title%' => $objective->getMessage()->getTitle()], 'email');
    }

    /**
     * @param MessageUpdatedEvent $objective
     */
    public function onNotificationSent($objective): void {
        $objective->getMessage()->setIsEmailNotificationSent(true);
        $this->messageRepository->persist($objective->getMessage());
    }

    /**
     * @param MessageUpdatedEvent $objective
     */
    public function getSender($objective): string {
        $creator = $objective->getMessage()->getUpdatedBy();

        if($creator === null) {
            return '';
        }

        return sprintf('%s %s', $creator->getFirstname(), $creator->getLastname());
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string {
        return 'email/message_update.html.twig';
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof MessageUpdatedEvent
            && $objective->getMessage()->getScope() === MessageScope::Messages
            && $objective->getMessage()->getStartDate() <= $this->dateHelper->getToday();
    }
}