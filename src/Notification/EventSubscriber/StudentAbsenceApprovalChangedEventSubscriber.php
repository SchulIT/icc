<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Event\StudentAbsenceApprovalChangedEvent;
use App\Notification\NotificationService;
use App\Notification\StudentAbsenceNotification;
use App\StudentAbsence\InvolvedUsersResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentAbsenceApprovalChangedEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly InvolvedUsersResolver $involvedUsersResolver, private readonly NotificationService $notificationService,
                                private readonly UrlGeneratorInterface $urlGenerator, private readonly TranslatorInterface $translator) {

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
                ->setUsername($event->getAbsence()->getEmail());
        }

        foreach($recipients as $recipient) {
            $notification = new StudentAbsenceNotification(
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
}