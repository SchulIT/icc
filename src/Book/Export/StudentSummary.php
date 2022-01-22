<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class StudentSummary {

    /**
     * @Serializer\Type("App\Book\Export\Student")
     * @Serializer\SerializedName("student")
     * @var Student
     */
    private $student;

    /**
     * @Serializer\SerializedName("absent_lessons_count")
     * @Serializer\Type("integer")
     * @var int
     */
    private $absentLessonsCount = 0;

    /**
     * @Serializer\SerializedName("not_excused_absent_lessons_count")
     * @Serializer\Type("integer")
     * @var int
     */
    private $notExcusedAbsentLessonCount = 0;

    /**
     * @Serializer\SerializedName("excuse_status_not_set_lessons_count")
     * @Serializer\Type("integer")
     * @var int
     */
    private $excuseStatusNotSetLessonCount = 0;

    /**
     * @Serializer\SerializedName("late_minutes_count")
     * @Serializer\Type("integer")
     * @var int
     */
    private $lateMinutesCount = 0;

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @param Student $student
     * @return StudentSummary
     */
    public function setStudent(Student $student): StudentSummary {
        $this->student = $student;
        return $this;
    }

    /**
     * @return int
     */
    public function getAbsentLessonsCount(): int {
        return $this->absentLessonsCount;
    }

    /**
     * @param int $absentLessonsCount
     * @return StudentSummary
     */
    public function setAbsentLessonsCount(int $absentLessonsCount): StudentSummary {
        $this->absentLessonsCount = $absentLessonsCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getNotExcusedAbsentLessonCount(): int {
        return $this->notExcusedAbsentLessonCount;
    }

    /**
     * @param int $notExcusedAbsentLessonCount
     * @return StudentSummary
     */
    public function setNotExcusedAbsentLessonCount(int $notExcusedAbsentLessonCount): StudentSummary {
        $this->notExcusedAbsentLessonCount = $notExcusedAbsentLessonCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getExcuseStatusNotSetLessonCount(): int {
        return $this->excuseStatusNotSetLessonCount;
    }

    /**
     * @param int $excuseStatusNotSetLessonCount
     * @return StudentSummary
     */
    public function setExcuseStatusNotSetLessonCount(int $excuseStatusNotSetLessonCount): StudentSummary {
        $this->excuseStatusNotSetLessonCount = $excuseStatusNotSetLessonCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getLateMinutesCount(): int {
        return $this->lateMinutesCount;
    }

    /**
     * @param int $lateMinutesCount
     * @return StudentSummary
     */
    public function setLateMinutesCount(int $lateMinutesCount): StudentSummary {
        $this->lateMinutesCount = $lateMinutesCount;
        return $this;
    }
}