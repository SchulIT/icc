<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class StudentSummary {

    #[Serializer\Type(Student::class)]
    #[Serializer\SerializedName('student')]
    private ?Student $student = null;

    #[Serializer\SerializedName('absent_lessons_count')]
    #[Serializer\Type('integer')]
    private int $absentLessonsCount = 0;

    #[Serializer\SerializedName('not_excused_absent_lessons_count')]
    #[Serializer\Type('integer')]
    private int $notExcusedAbsentLessonCount = 0;

    #[Serializer\SerializedName('excuse_status_not_set_lessons_count')]
    #[Serializer\Type('integer')]
    private int $excuseStatusNotSetLessonCount = 0;

    #[Serializer\SerializedName('late_minutes_count')]
    #[Serializer\Type('integer')]
    private int $lateMinutesCount = 0;

    #[Serializer\SerializedName('flags')]
    #[Serializer\Type('array<' . AttendanceFlagCount::class . '>')]
    private array $flagCounts = [ ];

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): StudentSummary {
        $this->student = $student;
        return $this;
    }

    public function getAbsentLessonsCount(): int {
        return $this->absentLessonsCount;
    }

    public function setAbsentLessonsCount(int $absentLessonsCount): StudentSummary {
        $this->absentLessonsCount = $absentLessonsCount;
        return $this;
    }

    public function getNotExcusedAbsentLessonCount(): int {
        return $this->notExcusedAbsentLessonCount;
    }

    public function setNotExcusedAbsentLessonCount(int $notExcusedAbsentLessonCount): StudentSummary {
        $this->notExcusedAbsentLessonCount = $notExcusedAbsentLessonCount;
        return $this;
    }

    public function getExcuseStatusNotSetLessonCount(): int {
        return $this->excuseStatusNotSetLessonCount;
    }

    public function setExcuseStatusNotSetLessonCount(int $excuseStatusNotSetLessonCount): StudentSummary {
        $this->excuseStatusNotSetLessonCount = $excuseStatusNotSetLessonCount;
        return $this;
    }

    public function getLateMinutesCount(): int {
        return $this->lateMinutesCount;
    }

    public function setLateMinutesCount(int $lateMinutesCount): StudentSummary {
        $this->lateMinutesCount = $lateMinutesCount;
        return $this;
    }

    /**
     * @return AttendanceFlagCount[]
     */
    public function getFlagCounts(): array {
        return $this->flagCounts;
    }

    /**
     * @param AttendanceFlagCount[] $flagCounts
     */
    public function setFlagCounts(array $flagCounts): StudentSummary {
        $this->flagCounts = $flagCounts;
        return $this;
    }
}