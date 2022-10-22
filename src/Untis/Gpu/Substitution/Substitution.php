<?php

namespace App\Untis\Gpu\Substitution;

use DateTime;

class Substitution {

    private ?int $id = null;

    private ?\DateTime $date = null;

    private ?\DateTime $lastChange = null;

    private ?int $lesson = null;

    /**
     * @var string[]
     */
    private array $rooms = [ ];

    /**
     * @var string[]
     */
    private array $replacementRooms = [ ];

    /**
     * @var string[]
     */
    private array $grades = [ ];

    /**
     * @var string[]
     */
    private array $replacementGrades = [ ];

    private ?string $subject = null;

    private ?string $replacementSubject = null;

    private ?string $teacher = null;

    private ?string $replacementTeacher = null;

    private int $flags = 0;

    private ?SubstitutionType $type = null;

    private ?string $remark = null;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): Substitution {
        $this->id = $id;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): Substitution {
        $this->date = $date;
        return $this;
    }

    public function getLastChange(): ?DateTime {
        return $this->lastChange;
    }

    public function setLastChange(?DateTime $lastChange): Substitution {
        $this->lastChange = $lastChange;
        return $this;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): Substitution {
        $this->lesson = $lesson;
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
    public function setRooms(array $rooms): Substitution {
        $this->rooms = $rooms;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getReplacementRooms(): array {
        return $this->replacementRooms;
    }

    /**
     * @param string[] $replacementRooms
     */
    public function setReplacementRooms(array $replacementRooms): Substitution {
        $this->replacementRooms = $replacementRooms;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @param string[] $grades
     */
    public function setGrades(array $grades): Substitution {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getReplacementGrades(): array {
        return $this->replacementGrades;
    }

    /**
     * @param string[] $replacementGrades
     */
    public function setReplacementGrades(array $replacementGrades): Substitution {
        $this->replacementGrades = $replacementGrades;
        return $this;
    }

    public function getSubject(): ?string {
        return $this->subject;
    }

    public function setSubject(?string $subject): Substitution {
        $this->subject = $subject;
        return $this;
    }

    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    public function setReplacementSubject(?string $replacementSubject): Substitution {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    public function getTeacher(): ?string {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): Substitution {
        $this->teacher = $teacher;
        return $this;
    }

    public function getReplacementTeacher(): ?string {
        return $this->replacementTeacher;
    }

    public function setReplacementTeacher(?string $replacementTeacher): Substitution {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }

    public function getFlags(): int {
        return $this->flags;
    }

    public function setFlags(int $flags): Substitution {
        $this->flags = $flags;
        return $this;
    }

    public function getType(): ?SubstitutionType {
        return $this->type;
    }

    public function setType(?SubstitutionType $type): Substitution {
        $this->type = $type;
        return $this;
    }

    public function getRemark(): ?string {
        return $this->remark;
    }

    public function setRemark(?string $remark): Substitution {
        $this->remark = $remark;
        return $this;
    }
}