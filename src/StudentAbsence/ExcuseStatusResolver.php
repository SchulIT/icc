<?php

namespace App\StudentAbsence;

use App\Book\Student\ExcuseCollectionResolver;
use App\Entity\DateLesson;
use App\Entity\LessonAttendance;
use App\Entity\StudentAbsence;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;

class ExcuseStatusResolver {
    public function __construct(private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository, private readonly LessonAttendanceRepositoryInterface $lessonAttendanceRepository, private readonly TimetableSettings $timetableSettings, private readonly ExcuseCollectionResolver $excuseCollectionResolver) {
    }

    public function getStatus(StudentAbsence $absence): ExcuseStatus {
        if($absence->getType()->isMustApprove()) {
            return new ExcuseStatus([], $absence->isApproved());
        }

        if($absence->getType()->isAlwaysExcused()) {
            return new ExcuseStatus([], true);
        }

        $lessonsToExcuse = $this->expandRangeToDateLessons($absence->getFrom(), $absence->getUntil());
        $collection = $this->excuseCollectionResolver->resolve($this->excuseNoteRepository->findByStudent($absence->getStudent()));

        $items = [ ];

        /** @var LessonAttendance[] $attendances */
        $attendances = ArrayUtils::createArrayWithKeys(
            $this->lessonAttendanceRepository->findByStudentAndDateRange($absence->getStudent(), $absence->getFrom()->getDate(), $absence->getUntil()->getDate()),
            function(LessonAttendance $attendance) {
                $keys = [];
                for($lesson = $attendance->getEntry()->getLessonStart(); $lesson <= $attendance->getEntry()->getLessonEnd(); $lesson++) {
                    $keys[] = sprintf('%s-%d', $attendance->getEntry()->getLesson()->getDate()->format('Y-m-d'), $lesson);
                }

                return $keys;
            }
        );

        foreach($lessonsToExcuse as $dateLesson) {
            $key = sprintf('%s-%d', $dateLesson->getDate()->format('Y-m-d'), $dateLesson->getLesson());
            $excuses = $collection[$key] ?? null;
            $attendance = $attendances[$key] ?? null;

            $items[] = new ExcuseStatusItem($dateLesson, $excuses, $attendance);
        }

        return new ExcuseStatus($items);
    }

    /**
     * @param DateLesson $start
     * @param DateLesson $end
     * @return DateLesson[]
     */
    private function expandRangeToDateLessons(DateLesson $start, DateLesson $end): array {
        $current = $start->clone();
        $dateLessons = [ ];

        while($current->getDate()->format('Y-m-d') !== $end->getDate()->format('Y-m-d') || $current->getLesson() != $end->getLesson()) {
            $dateLessons[] = $current;

            $current = $current->clone();
            if($current->getLesson() === $this->timetableSettings->getMaxLessons()) {
                $current->setLesson(1)->setDate((clone $current->getDate())->modify('+1 day'));
            } else {
                $current->setLesson($current->getLesson() + 1);
            }
        }

        return $dateLessons;
    }
}