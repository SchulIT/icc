<?php

namespace App\Notification\EventSubscriber;

use App\Event\MessageCreatedEvent;
use App\Message\MessageRecipientResolver;
use App\Notification\MessageNotification;
use App\Notification\NotificationService;
use App\Repository\MessageRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Notifies user when a new message is created
 */
class MessageCreatedEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly MessageRecipientResolver $recipientResolver,
                                private readonly TranslatorInterface $translator,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly NotificationService $notificationService,
                                private readonly DateHelper $dateHelper,
                                private readonly MessageRepositoryInterface $messageRepository) {    }

    public function onMessageCreated(MessageCreatedEvent $event): void {
        if($event->getMessage()->isEmailNotificationSent() === true) {
            return;
        }

        if($this->dateHelper->getToday() < $event->getMessage()->getStartDate()) {
            return;
        }

        foreach ($this->recipientResolver->resolveRecipients($event->getMessage()) as $recipient) {
            if($recipient->isMessageNotificationsEnabled() !== true) {
                continue;
            }

            $notification = new MessageNotification(
                $recipient,
                $this->translator->trans('message.create.title', [
                    '%title%' => $event->getMessage()->getTitle()
                ], 'email'),
                $this->translator->trans('message.create.content', [
                    '%title%' => $event->getMessage()->getTitle()
                ], 'email'),
                $this->urlGenerator->generate('show_message', ['uuid' => $event->getMessage()->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('message.create.link', [], 'email'),
                $event->getMessage()
            );

            $this->notificationService->notify($notification);
        }

        $message = $event->getMessage();
        $message->setIsEmailNotificationSent(true);
        $this->messageRepository->persist($message);
    }

    public static function getSubscribedEvents(): array {
        return [
            MessageCreatedEvent::class => 'onMessageCreated'
        ];
    }
}