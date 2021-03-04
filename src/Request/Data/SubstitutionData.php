<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubstitutionData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $id;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $date;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lessonStart;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThan(0)
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private $lessonEnd;

    /**
     * @Serializer\Type("boolean")
     * @var bool
     */
    private $startsBefore;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $type;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $subject;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $replacementSubject;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $teachers;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $replacementTeachers;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $rooms;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $replacementRooms;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $text;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $grades;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $replacementGrades;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return SubstitutionData
     */
    public function setId(?string $id): SubstitutionData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return SubstitutionData
     */
    public function setDate(\DateTime $date): SubstitutionData {
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
     * @return SubstitutionData
     */
    public function setLessonStart(int $lessonStart): SubstitutionData {
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
     * @return SubstitutionData
     */
    public function setLessonEnd(int $lessonEnd): SubstitutionData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return bool
     */
    public function startsBefore(): bool {
        return $this->startsBefore;
    }

    /**
     * @param bool $startsBefore
     * @return SubstitutionData
     */
    public function setStartsBefore(bool $startsBefore): SubstitutionData {
        $this->startsBefore = $startsBefore;
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
     * @return SubstitutionData
     */
    public function setType(?string $type): SubstitutionData {
        $this->type = $type;
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
     * @return SubstitutionData
     */
    public function setSubject(?string $subject): SubstitutionData {
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
     * @return SubstitutionData
     */
    public function setReplacementSubject(?string $replacementSubject): SubstitutionData {
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
     * @return SubstitutionData
     */
    public function setTeachers(array $teachers): SubstitutionData {
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
     * @return SubstitutionData
     */
    public function setReplacementTeachers(array $replacementTeachers): SubstitutionData {
        $this->replacementTeachers = $replacementTeachers;
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
     * @return SubstitutionData
     */
    public function setRooms(array $rooms): SubstitutionData {
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
     * @return SubstitutionData
     */
    public function setReplacementRooms(array $replacementRooms): SubstitutionData {
        $this->replacementRooms = $replacementRooms;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return SubstitutionData
     */
    public function setText(?string $text): SubstitutionData {
        $this->text = $text;
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
     * @return SubstitutionData
     */
    public function setGrades(array $grades): SubstitutionData {
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
     * @return SubstitutionData
     */
    public function setReplacementGrades(array $replacementGrades): SubstitutionData {
        $this->replacementGrades = $replacementGrades;
        return $this;
    }
}