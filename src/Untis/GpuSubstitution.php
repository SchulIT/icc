<?php

namespace App\Untis;

use DateTime;

class GpuSubstitution {

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
     * @var GpuSubstitutionType|null
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
     * @return GpuSubstitution
     */
    public function setId(int $id): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setDate(DateTime $date): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setLastChange(?DateTime $lastChange): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setLesson(int $lesson): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setRooms(array $rooms): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setReplacementRooms(array $replacementRooms): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setGrades(array $grades): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setReplacementGrades(array $replacementGrades): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setSubject(?string $subject): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setReplacementSubject(?string $replacementSubject): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setTeacher(?string $teacher): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setReplacementTeacher(?string $replacementTeacher): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setFlags(int $flags): GpuSubstitution {
        $this->flags = $flags;
        return $this;
    }

    /**
     * @return GpuSubstitutionType|null
     */
    public function getType(): ?GpuSubstitutionType {
        return $this->type;
    }

    /**
     * @param GpuSubstitutionType|null $type
     * @return GpuSubstitution
     */
    public function setType(?GpuSubstitutionType $type): GpuSubstitution {
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
     * @return GpuSubstitution
     */
    public function setRemark(?string $remark): GpuSubstitution {
        $this->remark = $remark;
        return $this;
    }
}