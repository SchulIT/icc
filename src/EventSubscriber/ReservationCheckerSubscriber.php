<?php

namespace App\EventSubscriber;

use App\Entity\Exam;
use App\Entity\ResourceReservation;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Event\SubstitutionImportEvent;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ResourceReservationRepositoryInterface;
use App\Rooms\Reservation\ResourceAvailabilityHelper;
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

    private ValidatorInterface $validator;
    private ResourceReservationRepositoryInterface $reservationRepository;
    private ExamRepositoryInterface $examRepository;

    private string $appName;
    private string $sender;

    private Swift_Mailer $mailer;
    private Environment $twig;
    private TranslatorInterface $translator;
    private DateHelper $dateHelper;

    private ResourceAvailabilityHelper $availabilityHelper;

    public function __construct(string $appName, string $sender, ValidatorInterface $validator, ResourceReservationRepositoryInterface $reservationRepository,
                                ExamRepositoryInterface $examRepository, Swift_Mailer $mailer, Environment $twig, TranslatorInterface $translator,
                                DateHelper $dateHelper, ResourceAvailabilityHelper $availabilityHelper) {
        $this->appName = $appName;
        $this->sender = $sender;

        $this->validator = $validator;
        $this->reservationRepository = $reservationRepository;
        $this->examRepository = $examRepository;

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;

        $this->dateHelper = $dateHelper;
        $this->availabilityHelper = $availabilityHelper;
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

                    if (count($violations) > 0 && $this->handleViolation($reservation) === false) {
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

    private function handleViolation(ResourceReservation $reservation): bool {
        $conflictingSubstitutions = 0;

        for($lesson = $reservation->getLessonStart(); $lesson <= $reservation->getLessonEnd(); $lesson++) {
            $availability = $this->availabilityHelper->getAvailability($reservation->getResource(), $reservation->getDate(), $lesson);

            if($availability === null) {
                continue;
            }

            if(($availability->getTimetableLesson() !== null && !$availability->isTimetableLessonCancelled()) || count($availability->getExams()) > 0) {
                return false;
            }

            $substitution = $availability->getSubstitution();
            if($substitution !== null) {
                $teachers = $substitution->getTeachers()->map(function (Teacher $teacher) {
                    return $teacher->getId();
                })->toArray();
                $replacementTeachers = $substitution->getReplacementTeachers()->map(function (Teacher $teacher) {
                    return $teacher->getId();
                })->toArray();

                if (!(count($replacementTeachers) > 0 && in_array($reservation->getTeacher()->getId(), $replacementTeachers) || count($replacementTeachers) === 0 && in_array($reservation->getTeacher()->getId(), $teachers))) {
                    $conflictingSubstitutions++;
                }
            }
        }

        if($conflictingSubstitutions === 0) {
            $this->sendReservationRemovedEmail($reservation);
            $this->reservationRepository->remove($reservation);

            return true;
        }

        return false;
    }

    private function sendReservationRemovedEmail(ResourceReservation $reservation): void {
        $content = $this->twig->render('email/reservation_removed.html.twig', [
            'reservation' => $reservation,
            'sender' => ''
        ]);

        $message = (new Swift_Message())
            ->setSubject($this->translator->trans('reservation_removed.title', [],'email'))
            ->setFrom([$this->sender], $this->appName)
            ->setSender($this->sender, $this->appName)
            ->setBody($content, 'text/html')
            ->setTo([ $reservation->getTeacher()->getEmail() ]);

        $this->mailer->send($message);
    }

    private function sendViolationsEmail(ResourceReservation $reservation, ConstraintViolationListInterface $violationList): void {
        $content = $this->twig->render('email/reservation.html.twig', [
            'reservation' => $reservation,
            'validation_errors' => $violationList,
            'sender' => ''
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
            'validation_errors' => $violationList,
            'sender' => ''
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
    public static function getSubscribedEvents(): array {
        return [
            SubstitutionImportEvent::class => 'onSubstitutionImportEvent'
        ];
    }
}