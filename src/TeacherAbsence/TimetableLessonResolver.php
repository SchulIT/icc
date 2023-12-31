<?php

namespace App\TeacherAbsence;

use App\Entity\TeacherAbsenceLesson;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\Repository\TeacherAbsenceLessonRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimetableLessonResolver {

    public function __construct(private readonly TimetableLessonRepositoryInterface $lessonRepository,
                                private readonly TeacherAbsenceLessonRepositoryInterface $absenceLessonRepository,
                                private readonly UserRepositoryInterface $userRepository,
                                private readonly NotificationService $notificationService,
                                private readonly TranslatorInterface $translator,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly LoggerInterface $logger) {

    }

    public function resolve(DateTime $start, DateTime $end): void {
        $absenceLessons = $this->absenceLessonRepository->findAllUnresolved($start, $end);

        foreach($absenceLessons as $absenceLesson) {
            if($absenceLesson->getLesson() !== null) {
                continue;
            }

            if($absenceLesson->getTuition() === null) {
                continue;
            }

            try {
                $timetableLesson = $this->lessonRepository->findOneByDateAndTeacher($absenceLesson->getDate(), $absenceLesson->getLessonStart(), $absenceLesson->getLessonEnd(), $absenceLesson->getAbsence()->getTeacher());

                if($timetableLesson === null || $timetableLesson->getTuition()?->getId() !== $absenceLesson->getTuition()->getId()) {
                    $this->log($absenceLesson);
                } else {
                    $absenceLesson->setLesson($timetableLesson);
                    $this->absenceLessonRepository->persist($absenceLesson);
                }
            } catch (Exception $exception) {
                $this->log($absenceLesson, $exception);
            }
        }
    }

    private function log(TeacherAbsenceLesson $absenceLesson, ?Exception $exception = null): void {
        $context = [ ];

        if($exception !== null) {
            $context = [
                'exception' => $exception
            ];
        }

        $this->logger->error(
            sprintf(
                'Die Stundenplanstunde für die Absenz von %s am %s (%d.-%d. Std. / %s) konnte nicht gefunden werden.',
                $absenceLesson->getAbsence()->getTeacher()->getAcronym(),
                $absenceLesson->getDate()->format('Y-m-d'),
                $absenceLesson->getLessonStart(),
                $absenceLesson->getLessonEnd(),
                $absenceLesson->getTuition()->getName()
            ),
            $context
        );

        try {
            $users = $this->userRepository->findAllTeachers([$absenceLesson->getAbsence()->getTeacher()]);

            foreach($users as $user) {
                $notification = new Notification(
                    $user,
                    $this->translator->trans('teacher_absence_lesson.error.title', [], 'email'),
                    $this->translator->trans('teacher_absence_lesson.error.content', [], 'email'),
                    $this->urlGenerator->generate('edit_teacher_absence_lesson', [
                        'uuid' => $absenceLesson->getUuid()
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                    $this->translator->trans('teacher_absence_lesson.link', [], 'email'),
                    true
                );

                $this->notificationService->notify($notification);
            }
        } catch (Exception $exception) {
            $this->logger->critical('Fehler beim Senden einer Benachrichtigung', [
                'exception' => $exception
            ]);
        }
    }
}