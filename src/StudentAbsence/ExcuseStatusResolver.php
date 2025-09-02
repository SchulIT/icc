<?php

namespace App\StudentAbsence;

use App\Book\Student\ExcuseCollectionResolver;
use App\Date\DateLessonExpander;
use App\Entity\DateLesson;
use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\LessonEntry;
use App\Entity\StudentAbsence;
use App\Entity\TimetableLesson;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;
use Doctrine\Common\Collections\ArrayCollection;

class ExcuseStatusResolver {
    public function __construct(private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                private readonly LessonAttendanceRepositoryInterface $lessonAttendanceRepository,
                                private readonly DateLessonExpander $dateLessonExpander,
                                private readonly ExcuseCollectionResolver $excuseCollectionResolver,
                                private readonly TimetableLessonRepositoryInterface $lessonRepository) {
    }

    public function getStatus(StudentAbsence $absence): ExcuseStatus {
        if($absence->getType()->getBookAttendanceType() === AttendanceType::Present) {
            return new ExcuseStatus([]);
        }

        $lessonsToExcuse = $this->dateLessonExpander->expandRangeToDateLessons($absence->getFrom(), $absence->getUntil());
        $collection = $this->excuseCollectionResolver->resolve($this->excuseNoteRepository->findByStudent($absence->getStudent()));

        $items = [ ];

        /** @var Attendance[] $attendances */
        $attendances = ArrayUtils::createArrayWithKeys(
            $this->lessonAttendanceRepository->findByStudentAndDateRange($absence->getStudent(), $absence->getFrom()->getDate(), $absence->getUntil()->getDate(), true),
            function(Attendance $attendance) {
                $keys = [];

                $start = $attendance->getEntry() !== null ? $attendance->getEntry()->getLessonStart() : $attendance->getEvent()->getLessonStart();
                $end = $attendance->getEntry() !== null ? $attendance->getEntry()->getLessonEnd() : $attendance->getEvent()->getLessonEnd();
                $date = $attendance->getEntry() !== null ? $attendance->getEntry()->getLesson()->getDate() : $attendance->getEvent()->getDate();

                for($lesson = $start; $lesson <= $end; $lesson++) {
                    $keys[] = sprintf('%s-%d', $date->format('Y-m-d'), $lesson);
                }

                return $keys;
            }
        );

        $timetableLessons = new ArrayCollection($this->lessonRepository->findAllByStudent($absence->getFrom()->getDate(), $absence->getUntil()->getDate(), $absence->getStudent()));

        foreach($lessonsToExcuse as $dateLesson) {
            $key = sprintf('%s-%d', $dateLesson->getDate()->format('Y-m-d'), $dateLesson->getLesson());
            $excuses = $collection[$key] ?? null;
            $attendance = $attendances[$key] ?? null;

            /** @var TimetableLesson|null $timetableLesson */
            $timetableLesson = $timetableLessons->findFirst(fn(int $idx, TimetableLesson $lesson) => $lesson->getDate() == $dateLesson->getDate() && $lesson->getLessonStart() <= $dateLesson->getLesson() && $dateLesson->getLesson() <= $lesson->getLessonEnd());
            $entry = $timetableLesson?->getEntries()->findFirst(fn(int $idx, LessonEntry $entry) => $entry->getLessonStart() <= $dateLesson->getLesson() && $dateLesson->getLesson() <= $entry->getLessonEnd());

            $items[] = new ExcuseStatusItem($dateLesson, $excuses, $attendance, $absence, $timetableLesson, $entry);
        }

        return new ExcuseStatus($items);
    }


}