<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use App\Event\ReturnItemReturnedEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\Student\RelatedUsersResolver;
use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ReturnItemReturnedEventSubscriber implements NotifierInterface, EventSubscriberInterface {

    public function __construct(private TranslatorInterface   $translator,
                                private UrlGeneratorInterface $urlGenerator,
                                private NotificationService   $notificationService,
                                private RelatedUsersResolver  $relatedUsersResolver) {

    }

    public function onReturnItemReturned(ReturnItemReturnedEvent $event): void {
        /** @var User[] $relatedUsers */
        $relatedUsers = array_merge(
            $this->relatedUsersResolver->resolveParents($event->getReturnItem()->getStudent()),
            $this->relatedUsersResolver->resolveStudents($event->getReturnItem()->getStudent()),
            $this->relatedUsersResolver->resolveGradeTeachers($event->getReturnItem()->getStudent(), $event->getReturnItem()->getCreatedAt())
        );

        foreach($relatedUsers as $user) {
            if($user->getId() === $event->getReturnItem()->getCreatedBy()->getId()) {
                continue; // don't notify the creator
            }

            $notification = new Notification(
                self::getKey(),
                $user,
                $this->translator->trans('return_item.returned.title', [], 'email'),
                $this->translator->trans('return_item.returned.content', [], 'email'),
                $this->urlGenerator->generate('show_return_item', ['uuid' => $event->getReturnItem()->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('return_item.link', [], 'email')
            );

            $this->notificationService->notify($notification);
        }
    }

    #[Override]
    public static function getSubscribedEvents(): array {
        return [
            ReturnItemReturnedEvent::class => 'onReturnItemReturned'
        ];
    }

    #[Override]
    public static function getSupportedRecipientUserTypes(): array {
        return [
            UserType::Teacher,
            UserType::Parent,
            UserType::Student
        ];
    }

    #[Override]
    public static function getKey(): string {
        return 'return_item_returned';
    }

    #[Override]
    public static function getLabelKey(): string {
        return 'notifications.return_item_returned.label';
    }

    #[Override]
    public static function getHelpKey(): string {
        return 'notifications.return_item_returned.help';
    }
}