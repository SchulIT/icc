<?php

namespace App\Response\Book;

use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceType;
use JMS\Serializer\Annotation as Serializer;

class AttendanceSuggestion {

    #[Serializer\Type(Student::class)]
    #[Serializer\SerializedName('student')]
    #[Serializer\ReadOnlyProperty]
    private readonly Student $student;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('label')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $label;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('attendance_type')]
    #[Serializer\ReadOnlyProperty]
    private readonly int $attendanceType;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('zero_absent_lessons')]
    #[Serializer\ReadOnlyProperty]
    private readonly bool $isZeroAbsentLessons;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('excuse_status')]
    #[Serializer\ReadOnlyProperty]
    private readonly int $excuseStatus;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('url')]
    #[Serializer\ReadOnlyProperty]
    private readonly ?string $url;

    #[Serializer\Type('array<int>')]
    #[Serializer\SerializedName('flags')]
    #[Serializer\ReadOnlyProperty]
    private readonly array $flags;

    public function __construct(Student $student, string $label, int $attendanceType, bool $isZeroAbsentLessons = false, int $excuseStatus = LessonAttendanceExcuseStatus::NotSet, ?string $url = null, array $flags = [ ]) {
        $this->student = $student;
        $this->attendanceType = $attendanceType;
        $this->label = $label;
        $this->isZeroAbsentLessons = $isZeroAbsentLessons;
        $this->excuseStatus = $excuseStatus;
        $this->url = $url;
        $this->flags = $flags;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @return int
     */
    public function getAttendanceType(): int {
        return $this->attendanceType;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isZeroAbsentLessons(): bool {
        return $this->isZeroAbsentLessons;
    }

    /**
     * @return int
     */
    public function getExcuseStatus(): int {
        return $this->excuseStatus;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string {
        return $this->url;
    }

    /**
     * @return int[]
     */
    public function getFlags(): array {
        return $this->flags;
    }
}