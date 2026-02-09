<?php

namespace App\StudentAbsence;

use App\Date\DateLessonExpander;
use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\LessonEntry;
use App\Entity\StudentAbsence;
use App\Entity\TimetableLesson;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Utils\ArrayUtils;
use Doctrine\Common\Collections\ArrayCollection;

readonly class ExcuseStatusResolver {
    public function __construct(private LessonAttendanceRepositoryInterface $lessonAttendanceRepository,
                                private DateLessonExpander $dateLessonExpander,
                                private TimetableLessonRepositoryInterface $lessonRepository) {
    }

    public function getStatus(StudentAbsence $absence): ExcuseStatus {
        if($absence->getType()->getBookAttendanceType() === AttendanceType::Present) {
            return new ExcuseStatus([]);
        }

        $lessonsToExcuse = $this->dateLessonExpander->expandRangeToDateLessons($absence->getFrom(), $absence->getUntil());
        $items = [ ];

        /** @var Attendance[] $attendances */
        $attendances = ArrayUtils::createArrayWithKeys(
            $this->lessonAttendanceRepository->findByStudentAndDateRange($absence->getStudent(), $absence->getFrom()->getDate(), $absence->getUntil()->getDate(), true),
            function(Attendance $attendance) {
                $lesson = $attendance->getLesson();
                $date = $attendance->getEntry() !== null ? $attendance->getEntry()->getLesson()->getDate() : $attendance->getEvent()->getDate();

                return sprintf('%s-%d', $date->format('Y-m-d'), $lesson);
            }
        );

        $timetableLessons = new ArrayCollection($this->lessonRepository->findAllByStudent($absence->getFrom()->getDate(), $absence->getUntil()->getDate(), $absence->getStudent()));

        foreach($lessonsToExcuse as $dateLesson) {
            $key = sprintf('%s-%d', $dateLesson->getDate()->format('Y-m-d'), $dateLesson->getLesson());
            $attendance = $attendances[$key] ?? null;

            /** @var TimetableLesson|null $timetableLesson */
            $timetableLesson = $timetableLessons->findFirst(fn(int $idx, TimetableLesson $lesson) => $lesson->getDate() == $dateLesson->getDate() && $lesson->getLessonStart() <= $dateLesson->getLesson() && $dateLesson->getLesson() <= $lesson->getLessonEnd());
            $entry = $timetableLesson?->getEntries()->findFirst(fn(int $idx, LessonEntry $entry) => $entry->getLessonStart() <= $dateLesson->getLesson() && $dateLesson->getLesson() <= $entry->getLessonEnd());

            $items[] = new ExcuseStatusItem($dateLesson, $attendance, $absence, $timetableLesson, $entry);
        }

        return new ExcuseStatus($items);
    }


}