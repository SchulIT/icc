<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Attendance {

    #[Serializer\Type(Student::class)]
    #[Serializer\SerializedName('student')]
    private ?Student $student = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('type')]
    private ?string $type = null;

    #[Serializer\Type('integer')]
    #[Serializer\SerializedName('lesson')]
    private int $lesson = 0;

    #[Serializer\Type('bool')]
    #[Serializer\SerializedName('zero_absent_lesson')]
    private bool $isZeroAbsentLesson = false;

    #[Serializer\Type('integer')]
    #[Serializer\SerializedName('late_minutes_count')]
    private int $lateMinutesCount = 0;

    #[Serializer\Type('boolean')]
    #[Serializer\SerializedName('is_excused')]
    private bool $isExcused = false;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('comment')]
    private ?string $comment = null;

    #[Serializer\Type('array<App\Book\Export\AttendanceFlag>')]
    #[Serializer\SerializedName('flags')]
    private array $flags = [ ];

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): Attendance {
        $this->student = $student;
        return $this;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): Attendance {
        $this->type = $type;
        return $this;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): Attendance {
        $this->lesson = $lesson;
        return $this;
    }

    public function isZeroAbsentLesson(): bool {
        return $this->isZeroAbsentLesson;
    }

    public function setIsZeroAbsentLesson(bool $isZeroAbsentLesson): Attendance {
        $this->isZeroAbsentLesson = $isZeroAbsentLesson;
        return $this;
    }

    public function getLateMinutesCount(): int {
        return $this->lateMinutesCount;
    }

    public function setLateMinutesCount(int $lateMinutesCount): Attendance {
        $this->lateMinutesCount = $lateMinutesCount;
        return $this;
    }

    public function isExcused(): bool {
        return $this->isExcused;
    }

    public function setIsExcused(bool $isExcused): Attendance {
        $this->isExcused = $isExcused;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): Attendance {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return AttendanceFlag[]
     */
    public function getFlags(): array {
        return $this->flags;
    }

    /**
     * @param AttendanceFlag[] $flags
     */
    public function setFlags(array $flags): void {
        $this->flags = $flags;
    }
}