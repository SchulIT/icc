<?php

namespace App\StudentAbsence;

use App\Entity\UserType;
use App\Event\StudentAbsenceApprovalChangedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApprovalNotifier extends AbstractNotifier implements EventSubscriberInterface {

    public function onApprovalChanged(StudentAbsenceApprovalChangedEvent $event): void {
        $absence = $event->getAbsence();

        $exclude = [ ];
        if($absence->getApprovedBy()->isTeacher()) {
            $exclude[] = $absence->getApprovedBy()->getTeacher()->getEmail();
        }

        $to = $this->getTeacherRecipients($absence, $exclude);

        if(!empty($absence->getEmail())) {
            $to[] = $absence->getEmail();
        }

        foreach($to as $recipient) {
            $email = (new TemplatedEmail())
                ->subject($this->translator->trans('absences.students.approval.title', [], 'email'))
                ->from(new Address($this->sender, $this->appName))
                ->sender(new Address($this->sender, $this->appName))
                ->to($recipient)
                ->htmlTemplate('email/absence_approval_changed.html.twig')
                ->textTemplate('email/absence_approval_changed.txt.twig')
                ->context([
                    'link' => $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absence->getUuid() ], UrlGeneratorInterface::ABSOLUTE_URL),
                    'type' => $absence->getType()->getName()
                ]);

            $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            StudentAbsenceApprovalChangedEvent::class => 'onApprovalChanged'
        ];
    }
}