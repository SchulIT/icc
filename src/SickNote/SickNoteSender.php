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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SickNoteSender implements EventSubscriberInterface {

    private string $sender;
    private string $appName;

    private StudentStringConverter $converter;
    private Environment $twig;
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private DateHelper $dateHelper;
    private UserStringConverter $userConverter;
    private SickNoteSettings $settings;
    private SectionResolverInterface $sectionResolver;

    private TokenStorageInterface $tokenStorage;

    public function __construct(string $sender, string $appName, StudentStringConverter $converter, Environment $twig, MailerInterface $mailer, TranslatorInterface $translator,
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

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function sendSickNote(SickNote $note, User $sender) {

        /** @var string[] $cc */
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

        $mail = (new Email())
            ->subject($this->translator->trans($isQuarantine ? 'sick_note.quarantine.title' : 'sick_note.sick.title', [
                '%student%' => $this->converter->convert($note->getStudent()),
                '%grade%' => $grade?->getName()
            ], 'email'))
            ->html($body)
            ->cc(...$cc)
            ->from(new Address($this->sender, $this->appName))
            ->sender(new Address($this->sender, $this->appName));

        if(!empty($this->settings->getRecipient())) {
            $mail->to($this->settings->getRecipient());
        }

        if(!empty($note->getEmail())) {
            $mail->bcc($note->getEmail());
            $mail->replyTo($note->getEmail());
        } else {
            $mail->replyTo($this->settings->getRecipient());
        }

        $this->mailer->send($mail);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
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