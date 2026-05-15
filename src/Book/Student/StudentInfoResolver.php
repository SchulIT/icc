<?php

namespace App\Book\Student;

use App\Book\Entity\Attendance;
use App\Book\Entity\AttendanceType;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Book\Repository\BookCommentRepositoryInterface;
use App\Book\Repository\LessonAttendanceRepositoryInterface;
use App\Book\Sorting\AttendanceStrategy;
use App\Framework\Sorting\Sorter;
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