<?php

namespace App\Book\Student;

use App\Entity\BookComment;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\Student;

class StudentInfo {

    /**
     * @param LessonAttendance[] $lateLessonAttendances
     * @param LessonAttendance[] $absentLessonAttendances
     * @param BookComment[] $comments
     */
    public function __construct(private Student $student, private int $totalLessonsCount, private array $lateLessonAttendances, private array $absentLessonAttendances, private array $comments)
    {
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getTotalLessonsCount(): int {
        return $this->totalLessonsCount;
    }

    /**
     * @return LessonAttendance[]
     */
    public function getLateLessonAttendances(): array {
        return $this->lateLessonAttendances;
    }

    /**
     * @return LessonAttendance[]
     */
    public function getAbsentLessonAttendances(): array {
        return $this->absentLessonAttendances;
    }

    public function getLateMinutesCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getLateMinutes(),
                $this->getLateLessonAttendances()
            )
        );
    }

    public function getAbsentLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => 1,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getNotExcusedOrNotSetLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::NotSet
                    && $attendance->getExcuses()->count() === 0 ? 1 : 0,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getNotExcusedAbsentLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::NotExcused
                    && $attendance->getExcuses()->count() === 0,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    /**
     * @return BookComment[]
     */
    public function getComments(): array {
        return $this->comments;
    }
}