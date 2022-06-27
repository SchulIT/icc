<?php

namespace App\Untis\Gpu\Tuition;

use DateTime;

class Tuition {
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
     * @return Tuition
     */
    public function setId(int $id): Tuition {
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
     * @return Tuition
     */
    public function setGrade(string $grade): Tuition {
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
     * @return Tuition
     */
    public function setTeacher(string $teacher): Tuition {
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
     * @return Tuition
     */
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
     * @return Tuition
     */
    public function setRooms(array $rooms): Tuition {
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
     * @return Tuition
     */
    public function setGroup(?string $group): Tuition {
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
     * @return Tuition
     */
    public function setValidFrom(?DateTime $validFrom): Tuition {
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
     * @return Tuition
     */
    public function setValidTo(?DateTime $validTo): Tuition {
        $this->validTo = $validTo;
        return $this;
    }
}