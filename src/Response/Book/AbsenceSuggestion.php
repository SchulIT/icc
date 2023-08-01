<?php

namespace App\Response\Book;

use App\Entity\LessonAttendanceExcuseStatus;
use JMS\Serializer\Annotation as Serializer;

class AbsenceSuggestion {

    #[Serializer\Type(Student::class)]
    #[Serializer\SerializedName('student')]
    #[Serializer\ReadOnlyProperty]
    private readonly Student $student;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('reason')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $reason;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('label')]
    #[Serializer\ReadOnlyProperty]
    private readonly ?string $label;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('zero_absent_lessons')]
    #[Serializer\ReadOnlyProperty]
    private readonly bool $isZeroAbsentLessons;

    #[Serializer\Type('int')]
    #[Serializer\SerializedName('excuse_status')]
    #[Serializer\ReadOnlyProperty]
    private readonly int $excuseStatus;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('absence_url')]
    #[Serializer\ReadOnlyProperty]
    private readonly ?string $absenceUrl;

    public function __construct(Student $student, string $reason, ?string $label = null, bool $isZeroAbsentLessons = false, int $excuseStatus = LessonAttendanceExcuseStatus::NotSet, ?string $absenceUrl = null) {
        $this->student = $student;
        $this->reason = $reason;
        $this->label = $label;
        $this->isZeroAbsentLessons = $isZeroAbsentLessons;
        $this->excuseStatus = $excuseStatus;
        $this->absenceUrl = $absenceUrl;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @return string
     */
    public function getReason(): string {
        return $this->reason;
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
    public function getAbsenceUrl(): ?string {
        return $this->absenceUrl;
    }
}