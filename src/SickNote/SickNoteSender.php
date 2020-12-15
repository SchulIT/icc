<?php

namespace App\SickNote;

use App\Converter\StudentStringConverter;
use App\Converter\UserStringConverter;
use App\Entity\GradeTeacher;
use App\Entity\User;
use App\Repository\SickNoteRepositoryInterface;
use App\Settings\SickNoteSettings;
use App\Timetable\TimetableTimeHelper;
use SchulIT\CommonBundle\Helper\DateHelper;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use App\Entity\SickNote as SickNoteEntity;

class SickNoteSender {

    private $sender;
    private $appName;

    private $converter;
    private $twig;
    private $mailer;
    private $translator;
    private $dateHelper;
    private $userConverter;
    private $settings;
    private $repository;
    private $timeHelper;

    public function __construct(string $sender, string $appName, StudentStringConverter $converter, Environment $twig, Swift_Mailer $mailer, TranslatorInterface $translator,
                                DateHelper $dateHelper, UserStringConverter $userConverter, SickNoteSettings $settings, SickNoteRepositoryInterface $repository, TimetableTimeHelper  $timeHelper) {
        $this->sender = $sender;
        $this->appName = $appName;
        $this->converter = $converter;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->dateHelper = $dateHelper;
        $this->userConverter = $userConverter;
        $this->settings = $settings;
        $this->repository = $repository;
        $this->timeHelper = $timeHelper;
    }

    private function persistInDatabase(SickNote $note): void {
        $entity = (new SickNoteEntity())
            ->setStudent($note->getStudent())
            ->setFrom($note->getFrom())
            ->setUntil($note->getUntil());
        $this->repository->persist($entity);
    }

    public function sendSickNote(SickNote $note, User $sender) {
        $this->persistInDatabase($note);

        $cc = [ ];
        $teachers = [ ];

        /** @var GradeTeacher $teacher */
        foreach($note->getStudent()->getGrade()->getTeachers() as $teacher) {
            $teachers[] = $teacher->getTeacher();
            $cc[] = $teacher->getTeacher()->getEmail();
        }

        $isQuarantine = $note->getReason()->equals(SickNoteReason::Quarantine());

        $body = $this->twig->render('email/sick_note.html.twig', [
            'note' => $note,
            'teachers' => $teachers,
            'sender' => $this->userConverter->convert($sender),
            'now' => $this->dateHelper->getNow(),
            'is_quarantine' => $isQuarantine
        ]);

        $message = (new Swift_Message())
            ->setSubject($this->translator->trans($isQuarantine ? 'sick_note.quarantine.title' : 'sick_note.sick.title', [
                '%student%' => $this->converter->convert($note->getStudent()),
                '%grade%' => $note->getStudent()->getGrade()->getName()
            ], 'email'))
            ->setBody($body, 'text/html')
            ->setCc($cc)
            ->setFrom([$this->sender], $this->appName)
            ->setSender($this->sender, $this->appName);

        if(!empty($this->settings->getRecipient())) {
            $message->setTo($this->settings->getRecipient());
        }

        if(!empty($note->getEmail())) {
            $message->addBcc($note->getEmail());
            $message->setReplyTo($note->getEmail());
        } else {
            $message->setReplyTo($this->settings->getRecipient());
        }

        foreach($note->getAttachments() as $attachment) {
            $message->attach(
                new Swift_Attachment(
                    file_get_contents($attachment->getRealPath()),
                    $attachment->getClientOriginalName()
                )
            );
        }

        $this->mailer->send($message);
    }
}