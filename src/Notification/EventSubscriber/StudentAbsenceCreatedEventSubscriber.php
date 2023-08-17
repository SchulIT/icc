<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Event\StudentAbsenceCreatedEvent;
use App\Notification\NotificationService;
use App\Notification\StudentAbsenceNotification;
use App\StudentAbsence\InvolvedUsersResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentAbsenceCreatedEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly InvolvedUsersResolver $involvedUsersResolver, private readonly NotificationService $notificationService,
                                private readonly UrlGeneratorInterface $urlGenerator, private readonly TranslatorInterface $translator) {

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
                ->setUsername($additionalRecipient);
        }

        foreach($recipients as $recipient) {
            $notification = new StudentAbsenceNotification(
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
}