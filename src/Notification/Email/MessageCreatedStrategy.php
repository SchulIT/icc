<?php

namespace App\Notification\Email;

use App\Event\MessageCreatedEvent;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageCreatedStrategy implements EmailStrategyInterface, PostEmailSendActionInterface {

    private $translator;
    private $userRepository;
    private $messageRepository;
    private $dateHelper;

    public function __construct(TranslatorInterface $translator, UserRepositoryInterface $userRepository, MessageRepositoryInterface $messageRepository, DateHelper $dateHelper) {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool {
        return true;
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string|null
     */
    public function getReplyTo($objective): ?string {
        $creator = $objective->getMessage()->getCreatedBy();

        if($creator === null) {
            return null;
        }

        return $creator->getEmail();
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return array
     */
    public function getRecipients($objective): array {
        if($objective->getMessage()->isEmailNotificationSent() || $objective->getMessage()->getStartDate() > $this->dateHelper->getToday()) {
            return [ ];
        }

        return $this->userRepository->findAllByNotifyMessages($objective->getMessage());
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string
     */
    public function getSubject($objective): string {
        return $this->translator->trans('message.create.title', ['%title%' => $objective->getMessage()->getTitle()], 'email');
    }

    /**
     * @param MessageCreatedEvent $objective
     * @return string
     */
    public function getSender($objective): string {
        $creator = $objective->getMessage()->getCreatedBy();

        if($creator === null) {
            return '';
        }

        return sprintf('%s %s', $creator->getFirstname(), $creator->getLastname());
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string {
        return 'email/message.html.twig';
    }

    /**
     * @param MessageCreatedEvent $objective
     */
    public function onNotificationSent($objective): void {
        $objective->getMessage()->setIsEmailNotificationSent(true);
        $this->messageRepository->persist($objective->getMessage());
    }

    /**
     * @inheritDoc
     */
    public function supports($objective): bool {
        return $objective instanceof MessageCreatedEvent;
    }
}