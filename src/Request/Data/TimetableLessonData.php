<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableLessonData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private string $id;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull
     * @var DateTime|null
     */
    private ?DateTime $date;

    /**
     * @Serializer\Type("int")
     * @Assert\NotNull()
     * @Assert\GreaterThan(0)
     * @var int
     */
    private int $lessonStart;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private int $lessonEnd;

    /**
     * @Serializer\Type("string")
     * @var string|null
     */
    private ?string $room;

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
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private ?string $subject;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return TimetableLessonData
     */
    public function setId(?string $id): TimetableLessonData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return TimetableLessonData
     */
    public function setDate(?DateTime $date): TimetableLessonData {
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
     * @return TimetableLessonData
     */
    public function setLessonStart(int $lessonStart): TimetableLessonData {
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
     * @return TimetableLessonData
     */
    public function setLessonEnd(int $lessonEnd): TimetableLessonData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoom(): ?string {
        return $this->room;
    }

    /**
     * @param string|null $room
     * @return TimetableLessonData
     */
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
     * @return TimetableLessonData
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
     * @return TimetableLessonData
     */
    public function setGrades(array $grades): TimetableLessonData {
        $this->grades = $grades;
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
     * @return TimetableLessonData
     */
    public function setSubject(?string $subject): TimetableLessonData {
        $this->subject = $subject;
        return $this;
    }
}