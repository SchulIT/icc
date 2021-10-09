<?php

namespace App\Untis;

use DateTime;

class GpuTuition {
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $grade;

    /**
     * @var string
     */
    private $teacher;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string[]
     */
    private $rooms = [ ];

    /**
     * @var string|null
     */
    private $group;

    /**
     * @var DateTime|null
     */
    private $validFrom;

    /**
     * @var DateTime|null
     */
    private $validTo;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GpuTuition
     */
    public function setId(int $id): GpuTuition {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getGrade(): string {
        return $this->grade;
    }

    /**
     * @param string $grade
     * @return GpuTuition
     */
    public function setGrade(string $grade): GpuTuition {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return string
     */
    public function getTeacher(): string {
        return $this->teacher;
    }

    /**
     * @param string $teacher
     * @return GpuTuition
     */
    public function setTeacher(string $teacher): GpuTuition {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return GpuTuition
     */
    public function setSubject(string $subject): GpuTuition {
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
     * @return GpuTuition
     */
    public function setRooms(array $rooms): GpuTuition {
        $this->rooms = $rooms;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGroup(): ?string {
        return $this->group;
    }

    /**
     * @param string|null $group
     * @return GpuTuition
     */
    public function setGroup(?string $group): GpuTuition {
        $this->group = $group;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getValidFrom(): ?DateTime {
        return $this->validFrom;
    }

    /**
     * @param DateTime|null $validFrom
     * @return GpuTuition
     */
    public function setValidFrom(?DateTime $validFrom): GpuTuition {
        $this->validFrom = $validFrom;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getValidTo(): ?DateTime {
        return $this->validTo;
    }

    /**
     * @param DateTime|null $validTo
     * @return GpuTuition
     */
    public function setValidTo(?DateTime $validTo): GpuTuition {
        $this->validTo = $validTo;
        return $this;
    }
}