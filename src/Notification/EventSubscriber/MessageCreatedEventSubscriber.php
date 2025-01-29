<?php

namespace App\Notification\EventSubscriber;

use App\Entity\UserType;
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
readonly class MessageCreatedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private MessageRecipientResolver $recipientResolver,
                                private TranslatorInterface $translator,
                                private UrlGeneratorInterface $urlGenerator,
                                private NotificationService $notificationService,
                                private DateHelper $dateHelper,
                                private MessageRepositoryInterface $messageRepository) {    }

    public function onMessageCreated(MessageCreatedEvent $event): void {
        if($event->preventDatabaseActions() === true) {
            return;
        }

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
                self::getKey(),
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

    public static function getSupportedRecipientUserTypes(): array {
        return UserType::cases();
    }

    public static function getKey(): string {
        return 'message_created';
    }

    public static function getLabelKey(): string {
        return 'notifications.message_created.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.message_created.help';
    }
}