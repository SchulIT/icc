<?php

namespace App\Notification\Email;

use App\Event\MessageUpdatedEvent;
use App\Repository\UserRepositoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageUpdatedStrategy implements EmailStrategyInterface {

    private $translator;
    private $userRepository;
    private $dateHelper;

    public function __construct(TranslatorInterface $translator, UserRepositoryInterface $userRepository, DateHelper $dateHelper) {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
        $this->dateHelper = $dateHelper;
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
        if($objective->getMessage()->getStartDate() > $this->dateHelper->getToday()) {
            return [ ];
        }

        return $this->userRepository->findAllByNotifyMessages($objective->getMessage());
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
        return $objective instanceof MessageUpdatedEvent;
    }
}