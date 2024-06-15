<?php

namespace App\Notification\EventSubscriber;

use App\Event\ChatMessageCreatedEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChatMessageCreatedEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly NotificationService $notificationService,
                                private readonly TranslatorInterface $translator,
                                private readonly UrlGeneratorInterface $urlGenerator) {

    }

    public function onChatMessageCreated(ChatMessageCreatedEvent $event): void {
        foreach($event->getMessage()->getChat()->getParticipants() as $participant) {
            if($participant->getId() === $event->getMessage()->getCreatedBy()?->getId()) {
                continue;
            }

            $notification = new Notification(
                $participant,
                $this->translator->trans('chat.message.create.title', [], 'email'),
                $this->translator->trans('chat.message.create.content', [], 'email'),
                $this->urlGenerator->generate('show_chat', ['uuid' => $event->getMessage()->getChat()->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('chat.message.link', [], 'email'),
                true
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            ChatMessageCreatedEvent::class => 'onChatMessageCreated'
        ];
    }
}