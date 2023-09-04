<?php

namespace App\Request\Data;

use App\Validator\TuitionResolvable;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[TuitionResolvable]
class TimetableLessonData {

    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private string $id;

    /**
     * @var DateTime|null
     */
    #[Assert\NotNull]
    #[Serializer\Type("DateTime<'Y-m-d\\TH:i:s'>")]
    private ?DateTime $date = null;

    /**
     * @var int
     */
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Serializer\Type('int')]
    private int $lessonStart;

    /**
     * @var int
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    #[Serializer\Type('int')]
    private int $lessonEnd;

    /**
     * @var string|null
     */
    #[Serializer\Type('string')]
    #[Assert\Length(max: 255)]
    private ?string $room = null;

    /**
     * @var string[]
     */
    #[Serializer\Type('array<string>')]
    private array $teachers = [ ];

    /**
     * @var string[]
     */
    #[Serializer\Type('array<string>')]
    private array $grades = [ ];


    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Serializer\Type('string')]
    #[Assert\Length(max: 255)]
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