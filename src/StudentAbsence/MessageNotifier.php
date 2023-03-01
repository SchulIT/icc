<?php

namespace App\StudentAbsence;

use App\Event\StudentAbsenceMessageCreatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Notifies teachers and parents/students when a new message is submitted.
 */
class MessageNotifier extends AbstractNotifier implements EventSubscriberInterface {

    public function onMessageCreated(StudentAbsenceMessageCreatedEvent $event): void {
        $absence = $event->getAbsence();

        $exclude = [ ];
        if($event->getMessage()->getCreatedBy()->isTeacher()) {
           $exclude[] = $event->getMessage()->getCreatedBy()->getTeacher()->getEmail();
        }

        $to = $this->getTeacherRecipients($absence, $exclude);

        if(!empty($absence->getEmail()) && $absence->getCreatedBy()->getId() !== $event->getMessage()->getCreatedBy()->getId()) {
            $to[] = $absence->getEmail();
        }

        foreach($to as $recipient) {
            $email = (new TemplatedEmail())
                ->subject($this->translator->trans('absences.students.message.title', [], 'email'))
                ->from(new Address($this->sender, $this->appName))
                ->sender(new Address($this->sender, $this->appName))
                ->to($recipient)
                ->htmlTemplate('email/new_absence_message.html.twig')
                ->textTemplate('email/new_absence_message.txt.twig')
                ->context([
                    'link' => $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absence->getUuid() ], UrlGeneratorInterface::ABSOLUTE_URL),
                    'type' => $absence->getType()->getName()
                ]);

            $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            StudentAbsenceMessageCreatedEvent::class => 'onMessageCreated'
        ];
    }
}