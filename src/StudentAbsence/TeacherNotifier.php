<?php

namespace App\StudentAbsence;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Event\StudentAbsenceCreatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Notifies teachers when a new absence is created
 */
class TeacherNotifier extends AbstractNotifier implements EventSubscriberInterface {

    public function onStudentAbsenceCreated(StudentAbsenceCreatedEvent $event): void {
        $absence = $event->getAbsence();
        $to = $this->getTeacherRecipients($absence);

        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('student_absence.create.title', ['%type%' => $absence->getType()->getName()], 'email'))
            ->from(new Address($this->sender, $this->appName))
            ->sender(new Address($this->sender, $this->appName))
            ->to(...$to)
            ->htmlTemplate('email/new_absence.html.twig')
            ->textTemplate('email/new_absence.txt.twig')
            ->context([
                'link' => $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absence->getUuid() ], UrlGeneratorInterface::ABSOLUTE_URL),
                'type' => $absence->getType()->getName()
            ]);

        if(!empty($this->settings->getRecipient())) {
            $email->cc($this->settings->getRecipient());
        }

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array {
        return [
            StudentAbsenceCreatedEvent::class => 'onStudentAbsenceCreated'
        ];
    }
}