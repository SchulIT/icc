<?php

namespace App\Book\Student;

use App\Entity\Section;
use App\Entity\Student;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\LessonAttendanceFlagRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use DateTime;

readonly class StudentStatisticsCounterResolver {
    public function __construct(
        private LessonAttendanceRepositoryInterface $lessonAttendanceRepository,
        private LessonAttendanceFlagRepositoryInterface $flagRepository,
        private BookCommentRepositoryInterface $commentRepository
    ) {

    }

    /**
     * @param Student[] $students
     * @param Section $section
     * @param array $tuitions
     * @param bool $includeEvents
     * @param DateTime|null $untilDate
     * @return StudentStatisticsCounter[]
     */
    public function resolveBulk(array $students, Section $section, array $tuitions = [], bool $includeEvents = false, DateTime|null $untilDate = null): array {
        $result = [ ];

        foreach($students as $student) {
            $result[] = $this->resolve($student, $section, $tuitions, $includeEvents, $untilDate);
        }

        return $result;
    }

    public function resolve(Student $student, Section $section, array $tuitions = [], bool $includeEvents = false, DateTime|null $untilDate = null): StudentStatisticsCounter {
        if($untilDate === null) {
            $untilDate = $section->getEnd();
        }

        $flagCounter = [ ];

        foreach($this->flagRepository->findAll() as $flag) {
            $flagCounter[$flag->getId()] = new AttendanceFlagCount(
                $flag,
                $this->lessonAttendanceRepository->countFlagByStudent($flag, $student, $section->getStart(), $untilDate, $includeEvents, $tuitions)
            );
        }

        return new StudentStatisticsCounter(
            $student,
            $this->lessonAttendanceRepository->countAllByStudent($student, $section->getStart(), $untilDate, $includeEvents, $tuitions),
            $this->lessonAttendanceRepository->countPresentByStudent($student, $section->getStart(), $untilDate, $includeEvents, $tuitions),
            $this->lessonAttendanceRepository->countAbsentByStudent($student, $section->getStart(), $untilDate, $includeEvents, $tuitions),
            $this->lessonAttendanceRepository->countLateMinutesByStudent($student, $section->getStart(), $untilDate, $includeEvents, $tuitions),
            $this->lessonAttendanceRepository->countNotExcusedLessonsCountByStudent($student, $section->getStart(), $untilDate, $includeEvents, $tuitions),
            $this->lessonAttendanceRepository->countExcuseStatusNotSetByStudent($student, $section->getStart(), $untilDate, $includeEvents, $tuitions),
            $this->commentRepository->countByDateAndStudent($student, $section->getStart(), $untilDate),
            $flagCounter,
        );
    }
}