<?php

namespace App\Notification\EventSubscriber;

use App\Converter\UserStringConverter;
use App\Event\AppointmentConfirmedEvent;
use App\Notification\AppointmentConfirmedNotification;
use App\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Notifies when an appointment is confirmed (e.g. by an administrator)
 */
class AppointmentConfirmedEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly NotificationService $notificationService,
                                private readonly TranslatorInterface $translator,
                                private readonly UserStringConverter $userStringConverter,
                                private readonly UrlGeneratorInterface $urlGenerator) {    }

    public function onAppointmentConfirmed(AppointmentConfirmedEvent $event): void {
        if($event->getAppointment()->getCreatedBy() === null) {
            return;
        }

        $notification = new AppointmentConfirmedNotification(
            $event->getAppointment()->getCreatedBy(),
            $this->translator->trans('appointment.title', [], 'email'),
            $this->translator->trans('appointment.content', [
                '%name%' => $event->getAppointment()->getTitle(),
                '%user%' => $this->userStringConverter->convert($event->getConfirmedBy())
            ], 'email'),
            $this->urlGenerator->generate('appointments', [], UrlGeneratorInterface::ABSOLUTE_URL),
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
}