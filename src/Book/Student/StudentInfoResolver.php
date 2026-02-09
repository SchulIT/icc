<?php

namespace App\Book\Student;

use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\Section;
use App\Entity\Student;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Sorting\AttendanceStrategy;
use App\Sorting\Sorter;
use DateTime;

readonly class StudentInfoResolver {

    public function __construct(
        private LessonAttendanceRepositoryInterface $attendanceRepository,
        private BookCommentRepositoryInterface $commentRepository,
        private StudentStatisticsCounterResolver $studentStatisticsCounterResolver,
        private Sorter $sorter
    ) { }

    public function resolveStudentInfo(Student $student, Section $section, array $tuitions = [], bool $includeEvents = false, DateTime|null $untilDate = null): StudentInfo {
        $attendances = $this->attendanceRepository->findByStudent($student, $section->getStart(), $untilDate ?? $section->getEnd(), $includeEvents, $tuitions);

        $late = array_filter($attendances, fn(Attendance $a) => $a->getType() === AttendanceType::Late);
        $absent = array_filter($attendances, fn(Attendance $a) => $a->getType() === AttendanceType::Absent);
        $present = array_filter($attendances, fn(Attendance $a) => $a->getType() === AttendanceType::Present);

        $this->sorter->sort($late, AttendanceStrategy::class);
        $this->sorter->sort($absent, AttendanceStrategy::class);

        $comments = $this->commentRepository->findAllByDateAndStudent($student, $section->getStart(), $untilDate ?? $section->getEnd());

        $counter = $this->studentStatisticsCounterResolver->resolve($student, $section, $tuitions, true, $untilDate);

        $callback = fn(Attendance $attendance) => new LessonAttendance($attendance->getDate(), $attendance->getLesson(), $attendance);

        return new StudentInfo(
            $student,
            $counter,
            array_map($callback, $late),
            array_map($callback, $absent),
            array_map($callback, $present),
            $comments
        );
    }
}