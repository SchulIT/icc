<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SubstitutionData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $id = null;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @Serializer\Type("int")
     */
    #[Assert\GreaterThan(0)]
    private ?int $lessonStart = null;

    /**
     * @Serializer\Type("int")
     */
    #[Assert\GreaterThan(0)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private ?int $lessonEnd = null;

    /**
     * @Serializer\Type("boolean")
     */
    private ?bool $startsBefore = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $type = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $subject = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $replacementSubject = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $teachers = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $replacementTeachers = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $rooms = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $replacementRooms = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $text = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $grades = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $replacementGrades = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): SubstitutionData {
        $this->id = $id;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): SubstitutionData {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): SubstitutionData {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): SubstitutionData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function startsBefore(): bool {
        return $this->startsBefore;
    }

    public function setStartsBefore(bool $startsBefore): SubstitutionData {
        $this->startsBefore = $startsBefore;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): SubstitutionData {
        $this->type = $type;
        return $this;
    }

    public function getSubject(): ?string {
        return $this->subject;
    }

    public function setSubject(?string $subject): SubstitutionData {
        $this->subject = $subject;
        return $this;
    }

    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

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
     */
    public function setReplacementRooms(array $replacementRooms): SubstitutionData {
        $this->replacementRooms = $replacementRooms;
        return $this;
    }

    public function getText(): ?string {
        return $this->text;
    }

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
     */
    public function setReplacementGrades(array $replacementGrades): SubstitutionData {
        $this->replacementGrades = $replacementGrades;
        return $this;
    }
}