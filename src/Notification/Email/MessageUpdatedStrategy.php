<?php

namespace App\Notification\Email;

use App\Event\MessageUpdatedEvent;
use App\Message\MessageRecipientResolver;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageUpdatedStrategy implements EmailStrategyInterface, PostEmailSendActionInterface {

    private $translator;
    private $userRepository;
    private $dateHelper;
    private $recipientResolver;
    private $messageRepository;

    public function __construct(TranslatorInterface $translator, MessageRecipientResolver $recipientResolver, UserRepositoryInterface $userRepository, DateHelper $dateHelper, MessageRepositoryInterface $messageRepository) {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
        $this->dateHelper = $dateHelper;
        $this->recipientResolver = $recipientResolver;
        $this->messageRepository = $messageRepository;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return true;
    }

    /**
     * @param MessageUpdatedEvent $objective
     * @return string|null
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
     * @return array
     */
    public function getRecipients($objective): array {
        return $this->recipientResolver->resolveRecipients($objective->getMessage());
    }

    /**
     * @param MessageUpdatedEvent $objective
     * @return string
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
     * @return string
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
            && $objective->getMessage()->getStartDate() <= $this->dateHelper->getToday();
    }
}