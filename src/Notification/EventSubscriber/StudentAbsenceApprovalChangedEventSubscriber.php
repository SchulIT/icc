<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use App\Event\StudentAbsenceApprovalChangedEvent;
use App\Notification\NotificationService;
use App\Notification\StudentAbsenceNotification;
use App\StudentAbsence\InvolvedUsersResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class StudentAbsenceApprovalChangedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private InvolvedUsersResolver $involvedUsersResolver, private NotificationService $notificationService,
                                private UrlGeneratorInterface $urlGenerator, private TranslatorInterface $translator) {

    }

    public function onStudentAbsenceApprovalChanged(StudentAbsenceApprovalChangedEvent $event): void {
        if($event->getAbsence()->getApprovedBy() === null) {
            return;
        }

        $recipients = [ ];
        foreach($this->involvedUsersResolver->resolveUsers($event->getAbsence()) as $user) {
            if($event->getAbsence()->getApprovedBy()->getId() !== $user->getId()) {
                $recipients[] = $user;
            }
        }

        $emails = array_map(fn(User $user) => $user->getEmail(), $recipients);
        if(!empty($event->getAbsence()->getEmail()) && !in_array($event->getAbsence()->getEmail(), $emails)) {
            $recipients[] = (new User())
                ->setEmail($event->getAbsence()->getEmail())
                ->setUsername($event->getAbsence()->getEmail())
                ->setUserType(UserType::User);
        }

        foreach($recipients as $recipient) {
            $notification = new StudentAbsenceNotification(
                self::getKey(),
                $recipient,
                $this->translator->trans('student_absence.approval.title', [], 'email'),
                $this->translator->trans('student_absence.approval.content', ['%type%' => $event->getAbsence()->getType()->getName()], 'email'),
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $event->getAbsence()->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('student_absence.link', [], 'email'),
                $event->getAbsence()
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            StudentAbsenceApprovalChangedEvent::class => 'onStudentAbsenceApprovalChanged'
        ];
    }

    public static function getSupportedRecipientUserTypes(): array {
        return [
            UserType::Student,
            UserType::Parent,
            UserType::Student
        ];
    }

    public static function getKey(): string {
        return 'student_absence_approval_changed';
    }

    public static function getLabelKey(): string {
        return 'notifications.student_absence_approval_changed.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.student_absence_approval_changed.help';
    }
}