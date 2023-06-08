<?php

namespace App\Notification\EventSubscriber;

use App\Event\MessageUpdatedEvent;
use App\Message\MessageRecipientResolver;
use App\Notification\MessageNotification;
use App\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageUpdatedEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly MessageRecipientResolver $recipientResolver,
                                private readonly TranslatorInterface $translator,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly NotificationService $notificationService) {    }

    public function onMessageUpdated(MessageUpdatedEvent $event): void {
        foreach ($this->recipientResolver->resolveRecipients($event->getMessage()) as $recipient) {
            if($recipient->isMessageNotificationsEnabled() !== true) {
                continue;
            }

            $notification = new MessageNotification(
                $recipient,
                $this->translator->trans('message.update.title', [
                    '%title%' => $event->getMessage()->getTitle()
                ], 'email'),
                $this->translator->trans('message.update.content', [
                    '%title%' => $event->getMessage()->getTitle()
                ], 'email'),
                $this->urlGenerator->generate('show_message', ['uuid' => $event->getMessage()->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('message.create.link', [], 'email'),
                $event->getMessage()
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            MessageUpdatedEvent::class => 'onMessageUpdated'
        ];
    }
}