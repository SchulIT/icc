<?php

namespace App\Notification\EventSubscriber;

use App\Entity\Exam;
use App\Entity\ResourceReservation;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Event\SubstitutionImportEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ResourceReservationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Rooms\Reservation\ResourceAvailabilityHelper;
use App\Validator\NoReservationCollision;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ReservationCheckerEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly ValidatorInterface $validator, private readonly ResourceReservationRepositoryInterface $reservationRepository,
                                private readonly ExamRepositoryInterface $examRepository, private readonly TranslatorInterface $translator,
                                private readonly DateHelper $dateHelper, private readonly ResourceAvailabilityHelper $availabilityHelper,
                                private readonly UserRepositoryInterface $userRepository, private readonly NotificationService $notificationService,
                                private readonly UrlGeneratorInterface $urlGenerator)
    {
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
                $teachers = $substitution->getTeachers()->map(fn(Teacher $teacher) => $teacher->getId())->toArray();
                $replacementTeachers = $substitution->getReplacementTeachers()->map(fn(Teacher $teacher) => $teacher->getId())->toArray();

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
        if($reservation->getTeacher() === null) {
            return;
        }

        foreach($this->userRepository->findAllTeachers([$reservation->getTeacher()]) as $recipient) {
            $notification = new Notification(
                $recipient,
                $this->translator->trans('reservation_removed.title', [], 'email'),
                $this->translator->trans('reservation_removed.content', [
                    '%resource%' => $reservation->getResource()->getName(),
                    '%date%' => $reservation->getDate()->format($this->translator->trans('date.format_short')),
                    '%lesson%' => $this->translator->trans('label.substitution_lessons', [
                        '%start%' => $reservation->getLessonStart(),
                        '%end%' => $reservation->getLessonEnd(),
                        '%count%' => ($reservation->getLessonEnd() - $reservation->getLessonStart())
                    ])
                ], 'email'),
                $this->urlGenerator->generate('substitutions', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('reservation_removed.link', [], 'email')
            );

            $this->notificationService->notify($notification);
        }
    }

    private function sendViolationsEmail(ResourceReservation $reservation, ConstraintViolationListInterface $violationList): void {
        if($reservation->getTeacher() === null) {
            return;
        }

        foreach($this->userRepository->findAllTeachers([$reservation->getTeacher()]) as $recipient) {
            $notification = new Notification(
                $recipient,
                $this->translator->trans('reservation.title', [], 'email'),
                $this->translator->trans('reservation.content', [
                    '%room%' => $reservation->getResource()->getName(),
                    '%date%' => $reservation->getDate()->format($this->translator->trans('date.format_short')),
                    '%lesson%' => $this->translator->trans('label.substitution_lessons', [
                        '%start%' => $reservation->getLessonStart(),
                        '%end%' => $reservation->getLessonEnd(),
                        '%count%' => ($reservation->getLessonEnd() - $reservation->getLessonStart())
                    ])
                ], 'email'),
                $this->urlGenerator->generate('edit_room_reservation', ['uuid' => $reservation->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('reservation.link', [], 'email')
            );

            $this->notificationService->notify($notification);
        }
    }

    /**
     * @param ConstraintViolationInterface[] $violationList
     */
    private function sendExamViolationsEmail(Exam $exam, array $violationList): void {
        $teachers = [ ];

        /** @var Tuition $tuition */
        foreach($exam->getTuitions() as $tuition) {
            foreach($tuition->getTeachers() as $teacher) {
                if(!in_array($teacher, $teachers)) {
                    $teachers[] = $teacher;
                }
            }
        }

        foreach($this->userRepository->findAllTeachers($teachers) as $recipient) {
            $notification = new Notification(
                $recipient,
                $this->translator->trans('reservation.title', [], 'email'),
                $this->translator->trans('reservation.content_exam', [
                    '%room%' => $exam->getRoom()?->getName(),
                    '%date%' => $exam->getDate()->format($this->translator->trans('date.format_short')),
                    '%lesson%' => $this->translator->trans('label.substitution_lessons', [
                        '%start%' => $exam->getLessonStart(),
                        '%end%' => $exam->getLessonEnd(),
                        '%count%' => ($exam->getLessonEnd() - $exam->getLessonStart())
                    ])
                ], 'email'),
                $this->urlGenerator->generate('edit_exam', ['uuid' => $exam->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('reservation.link', [], 'email')
            );

            $this->notificationService->notify($notification);
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