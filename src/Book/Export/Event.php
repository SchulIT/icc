<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Event {

    #[Serializer\Type('integer')]
    #[Serializer\SerializedName('start')]
    private int $start = 0;

    #[Serializer\Type('integer')]
    #[Serializer\SerializedName('end')]
    private int $end = 0;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('title')]
    private string $title;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('description')]
    private ?string $description = null;

    #[Serializer\Type(Teacher::class)]
    #[Serializer\SerializedName('teacher')]
    private ?Teacher $teacher = null;

    /**
     * @var Attendance[]
     */
    #[Serializer\Type('array<App\Book\Export\Attendance>')]
    #[Serializer\SerializedName('attendances')]
    private array $attendances = [ ];

    public function getStart(): int {
        return $this->start;
    }

    public function setStart(int $start): Event {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): int {
        return $this->end;
    }

    public function setEnd(int $end): Event {
        $this->end = $end;
        return $this;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): Event {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): Event {
        $this->description = $description;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): Event {
        $this->teacher = $teacher;
        return $this;
    }

    public function getAttendances(): array {
        return $this->attendances;
    }

    public function addAttendance(Attendance $attendance): void {
        $this->attendances[] = $attendance;
    }
}