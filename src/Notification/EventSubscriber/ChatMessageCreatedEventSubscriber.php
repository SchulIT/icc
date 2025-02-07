<?php

namespace App\Notification\EventSubscriber;

use App\Entity\UserType;
use App\Event\ChatMessageCreatedEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ChatMessageCreatedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private NotificationService $notificationService,
                                private TranslatorInterface $translator,
                                private UrlGeneratorInterface $urlGenerator) {

    }

    public function onChatMessageCreated(ChatMessageCreatedEvent $event): void {
        foreach($event->getMessage()->getChat()->getParticipants() as $participant) {
            if($participant->getId() === $event->getMessage()->getCreatedBy()?->getId()) {
                continue;
            }

            $notification = new Notification(
                self::getKey(),
                $participant,
                $this->translator->trans('chat.message.create.title', [], 'email'),
                $this->translator->trans('chat.message.create.content', [], 'email'),
                $this->urlGenerator->generate('show_chat', ['uuid' => $event->getMessage()->getChat()->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('chat.message.link', [], 'email')
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            ChatMessageCreatedEvent::class => 'onChatMessageCreated'
        ];
    }

    public static function getSupportedRecipientUserTypes(): array {
        return UserType::cases(); // all users are supported
    }

    public static function getKey(): string {
        return 'new_chat_message';
    }

    public static function getLabelKey(): string {
        return 'notifications.new_chat_message.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.new_chat_message.help';
    }
}