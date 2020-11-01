<?php

namespace App\EventSubscriber;

use App\Entity\Exam;
use App\Entity\RoomReservation;
use App\Entity\Substitution;
use App\Entity\Tuition;
use App\Event\SubstitutionImportEvent;
use App\Repository\ExamRepositoryInterface;
use App\Repository\RoomReservationRepositoryInterface;
use App\Validator\NoReservationCollision;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ReservationCheckerSubscriber implements EventSubscriberInterface {

    private $validator;
    private $reservationRepository;
    private $examRepository;

    private $appName;
    private $sender;

    private $mailer;
    private $twig;
    private $translator;
    private $dateHelper;

    public function __construct($appName, string $sender, ValidatorInterface $validator, RoomReservationRepositoryInterface $reservationRepository,
                                ExamRepositoryInterface $examRepository, Swift_Mailer $mailer, Environment $twig, TranslatorInterface $translator, DateHelper $dateHelper) {
        $this->appName = $appName;
        $this->sender = $sender;

        $this->validator = $validator;
        $this->reservationRepository = $reservationRepository;
        $this->examRepository = $examRepository;

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;

        $this->dateHelper = $dateHelper;
    }

    public function onSubstitutionImportEvent(SubstitutionImportEvent $event): void {
        $substitutions = array_merge($event->getAdded(), $event->getUpdated());
        /** @var DateTime $start */
        $start = null;

        /** @var DateTime $end */
        $end = null;

        /** @var Substitution $substitution */
        foreach($substitutions as $substitution) {
            if($start === null || $substitution->getDate() < $start) {
                $start = clone $substitution->getDate();
            }

            if($end === null || $substitution->getDate() > $end) {
                $end = clone $substitution->getDate();
            }
        }

        if($start === null || $end === null) {
            return;
        }

        $today = $this->dateHelper->getToday();

        while($start <= $end) {
            $reservations = $this->reservationRepository->findAllByDate($start);

            foreach($reservations as $reservation) {
                if($reservation->getDate() >= $today) {
                    $violations = $this->validator->validate($reservation);

                    if (count($violations) > 0) {
                        $this->sendViolationsEmail($reservation, $violations);
                    }
                }
            }

            $exams = $this->examRepository->findAllByDate($start);

            foreach($exams as $exam) {
                if(!empty($exam->getExternalId())) {
                    continue; // Disable check for external exams
                }

                $violations = $this->validator->validate($exam);


                $reservationViolations = [ ];

                foreach($violations as $violation) {
                    if($violation instanceof ConstraintViolation && $violation->getConstraint() instanceof NoReservationCollision) {
                        $reservationViolations[] = $violation;
                    }
                }

                if(count($reservationViolations) > 0) {
                    $this->sendExamViolationsEmail($exam, $reservationViolations);
                }
            }

            $start->modify('+1 day');
        }
    }

    private function sendViolationsEmail(RoomReservation $reservation, ConstraintViolationListInterface $violationList): void {
        $content = $this->twig->render('email/reservation.html.twig', [
            'reservation' => $reservation,
            'validation_errors' => $violationList
        ]);

        $message = (new Swift_Message())
            ->setSubject($this->translator->trans('reservation.title', [],'email'))
            ->setFrom([$this->sender], $this->appName)
            ->setSender($this->sender, $this->appName)
            ->setBody($content, 'text/html')
            ->setTo([ $reservation->getTeacher()->getEmail() ]);

        $this->mailer->send($message);
    }

    /**
     * @param Exam $exam
     * @param ConstraintViolationInterface[] $violationList
     */
    private function sendExamViolationsEmail(Exam $exam, array $violationList): void {
        $content = $this->twig->render('email/exam_reservation.html.twig', [
            'exam' => $exam,
            'validation_errors' => $violationList
        ]);

        $recipients = [ ];

        /** @var Tuition $tuition */
        foreach($exam->getTuitions() as $tuition) {
            foreach($tuition->getTeachers() as $teacher) {
                if(!in_array($teacher, $recipients)) {
                    $recipients[] = $teacher;
                }
            }
        }

        foreach($recipients as $recipient) {
            $message = (new Swift_Message())
                ->setSubject($this->translator->trans('reservation.title', [], 'email'))
                ->setFrom([$this->sender], $this->appName)
                ->setSender($this->sender, $this->appName)
                ->setBody($content, 'text/html')
                ->setTo([$recipient->getEmail()]);

            $this->mailer->send($message);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            SubstitutionImportEvent::class => 'onSubstitutionImportEvent'
        ];
    }
}