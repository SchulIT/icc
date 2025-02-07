<?php

namespace App\Notification\EventSubscriber;

use App\Converter\StudentStringConverter;
use App\Converter\TeacherStringConverter;
use App\Entity\Teacher;
use App\Entity\UserType;
use App\Event\ParentsDayAppointmentCancelledEvent;
use App\Event\ParentsDayAppointmentCreatedEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\ParentsDay\InvolvedUsersResolver;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ParentsDayAppointmentCreatedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private TranslatorInterface $translator,
                                private UrlGeneratorInterface $urlGenerator,
                                private NotificationService $notificationService,
                                private TeacherStringConverter $teacherStringConverter,
                                private StudentStringConverter $studentStringConverter,
                                private InvolvedUsersResolver $usersResolver,
                                private DateHelper $dateHelper) {

    }

    public function onParentsDayAppointmentCreated(ParentsDayAppointmentCreatedEvent $event): void {
        foreach($this->usersResolver->resolveUsers($event->getStudent(), $event->getAppointment()->getTeachers()->toArray(), $event->getInitiator()) as $recipient) {
            // no notifications to teachers if student/parents made an appointment
            if($event->getInitiator()->isStudentOrParent() && $recipient->isTeacher()) {
                continue;
            }

            if($event->getAppointment()->getParentsDay()->getBookingAllowedFrom() > $this->dateHelper->getToday()) {
                // do not notify before booking window starts
                continue;
            }

            $notification = new Notification(
                self::getKey(),
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

    public static function getSupportedRecipientUserTypes(): array {
        return [
            UserType::Teacher,
            UserType::Student,
            UserType::Parent
        ];
    }

    public static function getKey(): string {
        return 'parents_day_appointment_created';
    }

    public static function getLabelKey(): string {
        return 'notifications.parents_day_appointment_created.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.parents_day_appointment_created.help';
    }
}