<?php

namespace App\Book\Student;

use App\Entity\BookComment;
use App\Entity\Student;

class StudentInfo {

    /** @var Student */
    private $student;

    /** @var LessonAttendance[] */
    private $lateLessonAttendances;

    /** @var LessonAttendance[] */
    private $absentLessonAttendances;

    /** @var BookComment[] */
    private $comments;

    /**
     * @param Student $student
     * @param LessonAttendance[] $lateLessonAttendances
     * @param LessonAttendance[] $absentLessonAttendances
     * @param BookComment[] $comments
     */
    public function __construct(Student $student, array $lateLessonAttendances, array $absentLessonAttendances, array $comments) {
        $this->student = $student;
        $this->lateLessonAttendances = $lateLessonAttendances;
        $this->absentLessonAttendances = $absentLessonAttendances;
        $this->comments = $comments;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
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
                function(LessonAttendance $attendance) {
                    return $attendance->getAttendance()->getLateMinutes();
                },
                $this->getLateLessonAttendances()
            )
        );
    }

    public function getAbsentLessonsCount(): int {
        return array_sum(
            array_map(
                function(LessonAttendance $attendance) {
                    return 1;
                },
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getNotExcusedAbsentLessonsCount(): int {
        return array_sum(
            array_map(
                function(LessonAttendance $attendance) {
                    return $attendance->getExcuses()->count() === 0 ? 1 : 0;
                },
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