<?php

namespace App\Untis\Gpu\Substitution;

use DateTime;

class Substitution {

    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var DateTime|null
     */
    private $lastChange;

    /**
     * @var int
     */
    private $lesson;

    /**
     * @var string[]
     */
    private $rooms = [ ];

    /**
     * @var string[]
     */
    private $replacementRooms = [ ];

    /**
     * @var string[]
     */
    private $grades = [ ];

    /**
     * @var string[]
     */
    private $replacementGrades = [ ];

    /**
     * @var string|null
     */
    private $subject;

    /**
     * @var string|null
     */
    private $replacementSubject;

    /**
     * @var string|null
     */
    private $teacher;

    /**
     * @var string|null
     */
    private $replacementTeacher;

    /**
     * @var int
     */
    private $flags = 0;

    /**
     * @var SubstitutionType|null
     */
    private $type;

    /**
     * @var string|null
     */
    private $remark;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Substitution
     */
    public function setId(int $id): Substitution {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Substitution
     */
    public function setDate(DateTime $date): Substitution {
        $this->date = $date;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastChange(): ?DateTime {
        return $this->lastChange;
    }

    /**
     * @param DateTime|null $lastChange
     * @return Substitution
     */
    public function setLastChange(?DateTime $lastChange): Substitution {
        $this->lastChange = $lastChange;
        return $this;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param int $lesson
     * @return Substitution
     */
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
     * @return Substitution
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
     * @return Substitution
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
     * @return Substitution
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
     * @return Substitution
     */
    public function setReplacementGrades(array $replacementGrades): Substitution {
        $this->replacementGrades = $replacementGrades;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return Substitution
     */
    public function setSubject(?string $subject): Substitution {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    /**
     * @param string|null $replacementSubject
     * @return Substitution
     */
    public function setReplacementSubject(?string $replacementSubject): Substitution {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTeacher(): ?string {
        return $this->teacher;
    }

    /**
     * @param string|null $teacher
     * @return Substitution
     */
    public function setTeacher(?string $teacher): Substitution {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementTeacher(): ?string {
        return $this->replacementTeacher;
    }

    /**
     * @param string|null $replacementTeacher
     * @return Substitution
     */
    public function setReplacementTeacher(?string $replacementTeacher): Substitution {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }

    /**
     * @return int
     */
    public function getFlags(): int {
        return $this->flags;
    }

    /**
     * @param int $flags
     * @return Substitution
     */
    public function setFlags(int $flags): Substitution {
        $this->flags = $flags;
        return $this;
    }

    /**
     * @return SubstitutionType|null
     */
    public function getType(): ?SubstitutionType {
        return $this->type;
    }

    /**
     * @param SubstitutionType|null $type
     * @return Substitution
     */
    public function setType(?SubstitutionType $type): Substitution {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string {
        return $this->remark;
    }

    /**
     * @param string|null $remark
     * @return Substitution
     */
    public function setRemark(?string $remark): Substitution {
        $this->remark = $remark;
        return $this;
    }
}