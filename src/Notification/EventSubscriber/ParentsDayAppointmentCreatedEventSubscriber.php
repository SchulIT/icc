<?php

namespace App\Notification\EventSubscriber;

use App\Converter\StudentStringConverter;
use App\Converter\TeacherStringConverter;
use App\Entity\Teacher;
use App\Event\ParentsDayAppointmentCancelledEvent;
use App\Event\ParentsDayAppointmentCreatedEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\ParentsDay\InvolvedUsersResolver;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ParentsDayAppointmentCreatedEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly TranslatorInterface $translator,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly NotificationService $notificationService,
                                private readonly TeacherStringConverter $teacherStringConverter,
                                private readonly StudentStringConverter $studentStringConverter,
                                private readonly InvolvedUsersResolver $usersResolver) {

    }

    public function onParentsDayAppointmentCreated(ParentsDayAppointmentCreatedEvent $event): void {
        foreach($this->usersResolver->resolveUsers($event->getStudent(), $event->getAppointment()->getTeachers()->toArray(), $event->getInitiator()) as $recipient) {
            $notification = new Notification(
                $recipient,
                $this->translator->trans('parents_day.appointment.created.title', [], 'email'),
                $this->translator->trans('parents_day.appointment.created.content', [
                    '%start%' => $event->getAppointment()->getStart()->format('H:i'),
                    '%teacher%' => $this->getTeachers($event->getAppointment()->getTeachers()),
                    '%student%' => $this->studentStringConverter->convert($event->getStudent())
                ], 'email'),
                $this->urlGenerator->generate('parents_day', [
                    'day' => $event->getAppointment()->getParentsDay()->getUuid()->toString()
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('parents_day.link', [], 'email')
            );

            $this->notificationService->notify($notification);
        }
    }

    /**
     * @param Collection<Teacher> $teachers
     * @return string
     */
    private function getTeachers(Collection $teachers): string {
        return implode(
            ', ',
            array_map(
                fn(Teacher $teacher) => $this->teacherStringConverter->convert($teacher),
                $teachers->toArray()
            )
        );
    }

    public static function getSubscribedEvents(): array {
        return [
            ParentsDayAppointmentCreatedEvent::class => 'onParentsDayAppointmentCreated'
        ];
    }
}