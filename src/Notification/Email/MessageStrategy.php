<?php

namespace App\Notification\Email;

use App\Entity\Message;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageStrategy implements EmailStrategyInterface, PostEmailSendActionInterface {

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
     * @param Message $objective
     * @return string|null
     */
    public function getReplyTo($objective): ?string {
        $creator = $objective->getCreatedBy();

        if($creator === null) {
            return null;
        }

        return $creator->getEmail();
    }

    /**
     * @param Message $objective
     * @return array
     */
    public function getRecipients($objective): array {
        if($objective->isNotificationSent() || $objective->getStartDate() > $this->dateHelper->getToday()) {
            return [ ];
        }

        return $this->userRepository->findAllByNotifyMessages($objective);
    }

    /**
     * @param Message $objective
     * @return string
     */
    public function getSubject($objective): string {
        return $this->translator->trans('message.title', ['%title%' => $objective->getTitle()], 'email');
    }

    /**
     * @param Message $objective
     * @return string
     */
    public function getSender($objective): string {
        $creator = $objective->getCreatedBy();

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
     * @param Message $objective
     */
    public function onNotificationSent($objective): void {
        $objective->setIsNotificationSent(true);
        $this->messageRepository->persist($objective);
    }
}