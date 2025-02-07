<?php

namespace App\Notification\EventSubscriber;

use App\Entity\UserType;
use App\Event\MessageUpdatedEvent;
use App\Message\MessageRecipientResolver;
use App\Notification\MessageNotification;
use App\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class MessageUpdatedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private MessageRecipientResolver $recipientResolver,
                                private TranslatorInterface $translator,
                                private UrlGeneratorInterface $urlGenerator,
                                private NotificationService $notificationService) {    }

    public function onMessageUpdated(MessageUpdatedEvent $event): void {
        foreach ($this->recipientResolver->resolveRecipients($event->getMessage()) as $recipient) {
            if($recipient->isMessageNotificationsEnabled() !== true) {
                continue;
            }

            $notification = new MessageNotification(
                self::getKey(),
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

    public static function getSupportedRecipientUserTypes(): array {
        return UserType::cases();
    }

    public static function getKey(): string {
        return 'message_updated';
    }

    public static function getLabelKey(): string {
        return 'notifications.message_updated.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.message_updated.help';
    }
}