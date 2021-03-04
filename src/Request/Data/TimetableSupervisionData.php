<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableSupervisionData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $id;

    /**
     * Calendar week (1-53) this supervision takes place.
     * Note: weeks <1 or >53 are ignored without any warning.
     *
     * @Serializer\Type("array<integer>")
     * @Assert\Count(min="1")
     * @var int[]
     */
    private $weeks;

    /**
     * @Serializer\Type("int")
     * @Assert\NotNull()
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $day;

    /**
     * @Serializer\Type("boolean")
     * @var bool
     */
    private $isBefore = false;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $teacher;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lesson;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $location;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return TimetableSupervisionData
     */
    public function setId(?string $id): TimetableSupervisionData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getWeeks(): ?array {
        return $this->weeks;
    }

    /**
     * @param int[]|null $weeks
     * @return TimetableSupervisionData
     */
    public function setWeeks(?array $weeks): TimetableSupervisionData {
        $this->weeks = $weeks;
        return $this;
    }

    /**
     * @return int
     */
    public function getDay(): int {
        return $this->day;
    }

    /**
     * @param int $day
     * @return TimetableSupervisionData
     */
    public function setDay(int $day): TimetableSupervisionData {
        $this->day = $day;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBefore(): bool {
        return $this->isBefore;
    }

    /**
     * @param bool $isBefore
     * @return TimetableSupervisionData
     */
    public function setIsBefore(bool $isBefore): TimetableSupervisionData {
        $this->isBefore = $isBefore;
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
     * @return TimetableSupervisionData
     */
    public function setTeacher(?string $teacher): TimetableSupervisionData {
        $this->teacher = $teacher;
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
     * @return TimetableSupervisionData
     */
    public function setLesson(int $lesson): TimetableSupervisionData {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string {
        return $this->location;
    }

    /**
     * @param string|null $location
     * @return TimetableSupervisionData
     */
    public function setLocation(?string $location): TimetableSupervisionData {
        $this->location = $location;
        return $this;
    }
}