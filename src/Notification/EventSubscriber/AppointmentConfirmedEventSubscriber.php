<?php

namespace App\Notification\EventSubscriber;

use App\Converter\UserStringConverter;
use App\Entity\UserType;
use App\Event\AppointmentConfirmedEvent;
use App\Notification\AppointmentConfirmedNotification;
use App\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Notifies when an appointment is confirmed (e.g. by an administrator)
 */
readonly class AppointmentConfirmedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private NotificationService $notificationService,
                                private TranslatorInterface $translator,
                                private UserStringConverter $userStringConverter,
                                private UrlGeneratorInterface $urlGenerator) {    }

    public function onAppointmentConfirmed(AppointmentConfirmedEvent $event): void {
        if($event->getAppointment()->getCreatedBy() === null) {
            return;
        }

        $notification = new AppointmentConfirmedNotification(
            self::getKey(),
            $event->getAppointment()->getCreatedBy(),
            $this->translator->trans('appointment.title', [], 'email'),
            $this->translator->trans('appointment.content', [
                '%name%' => $event->getAppointment()->getTitle(),
                '%user%' => $this->userStringConverter->convert($event->getConfirmedBy())
            ], 'email'),
            $this->urlGenerator->generate('appointments', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->translator->trans('appointment.link', [], 'email'),
            $event->getAppointment(),
            $event->getConfirmedBy()
        );

        $this->notificationService->notify($notification);
    }

    public static function getSubscribedEvents(): array {
        return [
            AppointmentConfirmedEvent::class => 'onAppointmentConfirmed'
        ];
    }

    public static function getSupportedRecipientUserTypes(): array {
        return UserType::cases();
    }

    public static function getKey(): string {
        return 'appointment_confirmed';
    }

    public static function getLabelKey(): string {
        return 'notifications.appointment_confirmed.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.appointment_confirmed.help';
    }
}