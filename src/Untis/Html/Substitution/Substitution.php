<?php

namespace App\Untis\Html\Substitution;

use DateTime;

class Substitution {

    private int $id;

    private DateTime $date;

    private int $lessonStart;

    private int $lessonEnd;

    private bool $isSupervision;

    /** @var string[] */
    private array $rooms = [ ];

    /** @var string[] */
    private array $replacementRooms = [ ];

    /** @var string[] */
    private array $grades = [ ];

    /** @var string[] */
    private array $replacementGrades = [ ];

    private ?string $subject;

    private ?string $replacementSubject;

    /** @var string[] */
    private array $teachers;

    /** @var string[] */
    private array $replacementTeachers;

    private ?string $type;

    private ?string $remark;

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
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return Substitution
     */
    public function setLessonStart(int $lessonStart): Substitution {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return Substitution
     */
    public function setLessonEnd(int $lessonEnd): Substitution {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSupervision(): bool {
        return $this->isSupervision;
    }

    /**
     * @param bool $isSupervision
     * @return Substitution
     */
    public function setIsSupervision(bool $isSupervision): Substitution {
        $this->isSupervision = $isSupervision;
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
     * @return string[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param string[] $teachers
     * @return Substitution
     */
    public function setTeachers(array $teachers): Substitution {
        $this->teachers = $teachers;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getReplacementTeachers(): array {
        return $this->replacementTeachers;
    }

    /**
     * @param string[] $replacementTeachers
     * @return Substitution
     */
    public function setReplacementTeachers(array $replacementTeachers): Substitution {
        $this->replacementTeachers = $replacementTeachers;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return Substitution
     */
    public function setType(?string $type): Substitution {
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