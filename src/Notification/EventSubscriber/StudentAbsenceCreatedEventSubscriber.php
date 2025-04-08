<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use App\Event\StudentAbsenceCreatedEvent;
use App\Notification\NotificationService;
use App\Notification\StudentAbsenceNotification;
use App\StudentAbsence\InvolvedUsersResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class StudentAbsenceCreatedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private InvolvedUsersResolver $involvedUsersResolver, private NotificationService $notificationService,
                                private UrlGeneratorInterface $urlGenerator, private TranslatorInterface $translator) {

    }

    public function onStudentAbsenceCreated(StudentAbsenceCreatedEvent $event): void {
        $recipients = [ ];
        foreach($this->involvedUsersResolver->resolveUsers($event->getAbsence()) as $user) {
            if($event->getAbsence()->getCreatedBy()->getId() !== $user->getId()) {
                $recipients[] = $user;
            }
        }

        foreach($event->getAbsence()->getType()->getAdditionalRecipients() as $additionalRecipient) {
            $recipients[] = (new User())
                ->setEmail($additionalRecipient)
                ->setUsername($additionalRecipient)
                ->setUserType(UserType::User);
        }

        foreach($recipients as $recipient) {
            $notification = new StudentAbsenceNotification(
                self::getKey(),
                $recipient,
                $this->translator->trans('student_absence.create.title', ['%type%' => $event->getAbsence()->getType()->getName()], 'email'),
                $this->translator->trans('student_absence.create.content', ['%type%' => $event->getAbsence()->getType()->getName()], 'email'),
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $event->getAbsence()->getUuid() ], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('student_absence.link', [], 'email'),
                $event->getAbsence(),
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            StudentAbsenceCreatedEvent::class => 'onStudentAbsenceCreated'
        ];
    }

    public static function getSupportedRecipientUserTypes(): array {
        return [
            UserType::Teacher,
            UserType::Parent,
            UserType::Student
        ];
    }

    public static function getKey(): string {
        return 'student_absence_created';
    }

    public static function getLabelKey(): string {
        return 'notifications.student_absence_created.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.student_absence_created.help';
    }
}