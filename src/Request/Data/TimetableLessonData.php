<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableLessonData {

    /**
     * @Serializer\Type("string")
     * @var string
     */
    #[Assert\NotBlank]
    private string $id;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @var DateTime|null
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @Serializer\Type("int")
     * @var int
     */
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private int $lessonStart;

    /**
     * @Serializer\Type("int")
     * @var int
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private int $lessonEnd;

    /**
     * @Serializer\Type("string")
     * @var string|null
     */
    private ?string $room = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $teachers = [ ];

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $grades = [ ];


    /**
     * @Serializer\Type("string")
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $subject = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): TimetableLessonData {
        $this->id = $id;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): TimetableLessonData {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): TimetableLessonData {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): TimetableLessonData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getRoom(): ?string {
        return $this->room;
    }

    public function setRoom(?string $room): TimetableLessonData {
        $this->room = $room;
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
    public function setTeachers(array $teachers): TimetableLessonData {
        $this->teachers = $teachers;
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
    public function setGrades(array $grades): TimetableLessonData {
        $this->grades = $grades;
        return $this;
    }

    public function getSubject(): ?string {
        return $this->subject;
    }

    public function setSubject(?string $subject): TimetableLessonData {
        $this->subject = $subject;
        return $this;
    }
}