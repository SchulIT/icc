<?php

namespace App\Untis\Html;

use DateTime;

class HtmlSubstitution {

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
     * @return HtmlSubstitution
     */
    public function setId(int $id): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setDate(DateTime $date): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setLessonStart(int $lessonStart): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setLessonEnd(int $lessonEnd): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setIsSupervision(bool $isSupervision): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setRooms(array $rooms): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setReplacementRooms(array $replacementRooms): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setGrades(array $grades): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setReplacementGrades(array $replacementGrades): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setSubject(?string $subject): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setReplacementSubject(?string $replacementSubject): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setTeachers(array $teachers): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setReplacementTeachers(array $replacementTeachers): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setType(?string $type): HtmlSubstitution {
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
     * @return HtmlSubstitution
     */
    public function setRemark(?string $remark): HtmlSubstitution {
        $this->remark = $remark;
        return $this;
    }
}