<?php

namespace App\Untis\Gpu\Tuition;

use DateTime;

class Tuition {
    private ?int $id = null;

    private ?string $grade = null;

    private ?string $teacher = null;

    private ?string $subject = null;

    /**
     * @var string[]
     */
    private array $rooms = [ ];

    private ?string $group = null;

    private ?DateTime $validFrom = null;

    private ?DateTime $validTo = null;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): Tuition {
        $this->id = $id;
        return $this;
    }

    public function getGrade(): string {
        return $this->grade;
    }

    public function setGrade(string $grade): Tuition {
        $this->grade = $grade;
        return $this;
    }

    public function getTeacher(): string {
        return $this->teacher;
    }

    public function setTeacher(string $teacher): Tuition {
        $this->teacher = $teacher;
        return $this;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): Tuition {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }

    /**
     * @param string[] $rooms
     */
    public function setRooms(array $rooms): Tuition {
        $this->rooms = $rooms;
        return $this;
    }

    public function getGroup(): ?string {
        return $this->group;
    }

    public function setGroup(?string $group): Tuition {
        $this->group = $group;
        return $this;
    }

    public function getValidFrom(): ?DateTime {
        return $this->validFrom;
    }

    public function setValidFrom(?DateTime $validFrom): Tuition {
        $this->validFrom = $validFrom;
        return $this;
    }

    public function getValidTo(): ?DateTime {
        return $this->validTo;
    }

    public function setValidTo(?DateTime $validTo): Tuition {
        $this->validTo = $validTo;
        return $this;
    }
}