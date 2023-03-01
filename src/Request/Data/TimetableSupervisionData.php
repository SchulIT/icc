<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableSupervisionData {

    /**
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getId", setter="setId")
     */
    #[Serializer\Accessor(getter: 'getId', setter: 'setId')]
    #[Assert\NotBlank]
    private ?string $id = null;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @Serializer\Type("boolean")
     */
    private bool $isBefore = false;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $teacher = null;

    /**
     * @Serializer\Type("int")
     */
    #[Assert\GreaterThan(0)]
    private ?int $lesson = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $location = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): TimetableSupervisionData {
        $this->id = $id;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): TimetableSupervisionData {
        $this->date = $date;
        return $this;
    }

    public function isBefore(): bool {
        return $this->isBefore;
    }

    public function setIsBefore(bool $isBefore): TimetableSupervisionData {
        $this->isBefore = $isBefore;
        return $this;
    }

    public function getTeacher(): ?string {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): TimetableSupervisionData {
        $this->teacher = $teacher;
        return $this;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): TimetableSupervisionData {
        $this->lesson = $lesson;
        return $this;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function setLocation(?string $location): TimetableSupervisionData {
        $this->location = $location;
        return $this;
    }
}