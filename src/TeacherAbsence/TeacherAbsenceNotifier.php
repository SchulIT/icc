<?php

namespace App\TeacherAbsence;

use App\Event\TeacherAbsenceCreatedEvent;
use App\Event\TeacherAbsenceUpdatedEvent;
use App\Settings\TeacherAbsenceSettings;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeacherAbsenceNotifier implements EventSubscriberInterface {

    public function __construct(private readonly string $sender, private readonly string $appName, private readonly MailerInterface $mailer, private readonly TeacherAbsenceSettings $settings, private readonly TranslatorInterface $translator, private readonly UrlGeneratorInterface $urlGenerator) { }

    public function onTeacherAbsenceCreated(TeacherAbsenceCreatedEvent $event): void {
        $absence = $event->getAbsence();

        $recipients = $this->settings->getOnCreateRecipients();

        if(empty($recipients)) {
            return;
        }

        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('teacher_absence.create.title', [], 'email'))
            ->from(new Address($this->sender, $this->appName))
            ->sender(new Address($this->sender, $this->appName))
            ->to(...$recipients)
            ->textTemplate('email/new_teacher_absence.txt.twig')
            ->context([
                'link' => $this->urlGenerator->generate('show_teacher_absence', ['uuid' => $absence->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        if(!empty($absence->getTeacher()?->getEmail())) {
            $email->replyTo($absence->getTeacher()->getEmail());
        }

        $this->mailer->send($email);
    }

    public function onTeacherAbsenceUpdated(TeacherAbsenceUpdatedEvent $event): void {
        $absence = $event->getAbsence();

        $recipients = $this->settings->getOnUpdateRecipients();

        if(empty($recipients)) {
            return;
        }

        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('teacher_absence.update.title', [], 'email'))
            ->from(new Address($this->sender, $this->appName))
            ->sender(new Address($this->sender, $this->appName))
            ->to(...$recipients)
            ->textTemplate('email/teacher_absence_updated.txt.twig')
            ->context([
                'link' => $this->urlGenerator->generate('show_teacher_absence', ['uuid' => $absence->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        if(!empty($absence->getTeacher()?->getEmail())) {
            $email->replyTo($absence->getTeacher()->getEmail());
        }

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array {
        return [
            TeacherAbsenceCreatedEvent::class => 'onTeacherAbsenceCreated',
            TeacherAbsenceUpdatedEvent::class => 'onTeacherAbsenceUpdated'
        ];
    }
}