<?php

namespace App\SickNote;

use App\Converter\StudentStringConverter;
use App\Converter\UserStringConverter;
use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\SickNote;
use App\Entity\SickNoteReason;
use App\Entity\User;
use App\Event\SickNoteCreatedEvent;
use App\Section\SectionResolverInterface;
use App\Settings\SickNoteSettings;
use SchulIT\CommonBundle\Helper\DateHelper;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SickNoteSender implements EventSubscriberInterface {

    private $sender;
    private $appName;

    private $converter;
    private $twig;
    private $mailer;
    private $translator;
    private $dateHelper;
    private $userConverter;
    private $settings;
    private $sectionResolver;

    private $tokenStorage;

    public function __construct(string $sender, string $appName, StudentStringConverter $converter, Environment $twig, Swift_Mailer $mailer, TranslatorInterface $translator,
                                DateHelper $dateHelper, UserStringConverter $userConverter, SickNoteSettings $settings, SectionResolverInterface $sectionResolver, TokenStorageInterface $tokenStorage) {
        $this->sender = $sender;
        $this->appName = $appName;
        $this->converter = $converter;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->dateHelper = $dateHelper;
        $this->userConverter = $userConverter;
        $this->settings = $settings;
        $this->sectionResolver = $sectionResolver;
        $this->tokenStorage = $tokenStorage;
    }

    private function sendSickNote(SickNote $note, User $sender) {

        $cc = [ ];
        $teachers = [ ];

        $section = $this->sectionResolver->getCurrentSection();

        /** @var Grade|null $grade */
        $grade = $note->getStudent()->getGrade($section);
        if($grade !== null && $section !== null) {
            /** @var GradeTeacher $teacher */
            foreach ($grade->getTeachers() as $teacher) {
                if($teacher->getSection()->getId() === $section->getId()) {
                    $teachers[] = $teacher->getTeacher();
                    $cc[] = $teacher->getTeacher()->getEmail();
                }
            }
        }

        $isQuarantine = $note->getReason()->equals(SickNoteReason::Quarantine());

        $grade = $note->getStudent()->getGrade($section);
        $gradeName = $this->translator->trans('label.not_available');
        if($grade !== null) {
            $gradeName = $grade->getName();
        }

        $body = $this->twig->render('email/sick_note.html.twig', [
            'note' => $note,
            'teachers' => $teachers,
            'sender' => $this->userConverter->convert($sender),
            'now' => $this->dateHelper->getNow(),
            'is_quarantine' => $isQuarantine,
            'section' => $section,
            'grade' => $gradeName
        ]);

        $message = (new Swift_Message())
            ->setSubject($this->translator->trans($isQuarantine ? 'sick_note.quarantine.title' : 'sick_note.sick.title', [
                '%student%' => $this->converter->convert($note->getStudent()),
                '%grade%' => $grade !== null ? $grade->getName() : null
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

        $this->mailer->send($message);
    }

    public function onSickNoteCreated(SickNoteCreatedEvent $event) {
        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return;
        }

        $sender = $token->getUser();

        if(!$sender instanceof User) {
            return;
        }

        $this->sendSickNote($event->getSickNote(), $sender);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            SickNoteCreatedEvent::class => 'onSickNoteCreated'
        ];
    }
}