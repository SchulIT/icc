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

    private ?string $subject = null;

    private ?string $replacementSubject = null;

    /** @var string[] */
    private array $teachers;

    /** @var string[] */
    private array $replacementTeachers;

    private ?string $type = null;

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

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): Substitution {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): Substitution {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function isSupervision(): bool {
        return $this->isSupervision;
    }

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

    /**
     * @return string[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param string[] $teachers
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
     */
    public function setReplacementTeachers(array $replacementTeachers): Substitution {
        $this->replacementTeachers = $replacementTeachers;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): Substitution {
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