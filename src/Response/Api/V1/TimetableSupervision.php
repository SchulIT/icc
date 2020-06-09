<?php

namespace App\Response\Api\V1;

use App\Entity\TimetableSupervision as TimetableSupervisionEntity;
use JMS\Serializer\Annotation as Serializer;

class TimetableSupervision {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("week")
     * @Serializer\Type("App\Response\Api\V1\TimetableWeek")
     * @var TimetableWeek
     */
    private $week;

    /**
     * @Serializer\SerializedName("day")
     * @Serializer\Type("int")
     * @var int
     */
    private $day;

    /**
     * @Serializer\SerializedName("lesson")
     * @Serializer\Type("int")
     * @var int
     */
    private $lesson;

    /**
     * @Serializer\SerializedName("is_before")
     * @Serializer\Type("bool")
     * @var bool
     */
    private $isBefore;

    /**
     * @Serializer\SerializedName("teacher")
     * @Serializer\Type("App\Response\Api\V1\Teacher")
     * @var Teacher
     */
    private $teacher;

    /**
     * @Serializer\SerializedName("location")
     * @Serializer\Type("string")
     * @var string
     */
    private $location;

    /**
     * @return TimetableWeek
     */
    public function getWeek(): TimetableWeek {
        return $this->week;
    }

    /**
     * @param TimetableWeek $week
     * @return TimetableSupervision
     */
    public function setWeek(TimetableWeek $week): TimetableSupervision {
        $this->week = $week;
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
     * @return TimetableSupervision
     */
    public function setDay(int $day): TimetableSupervision {
        $this->day = $day;
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
     * @return TimetableSupervision
     */
    public function setLesson(int $lesson): TimetableSupervision {
        $this->lesson = $lesson;
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
     * @return TimetableSupervision
     */
    public function setIsBefore(bool $isBefore): TimetableSupervision {
        $this->isBefore = $isBefore;
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return TimetableSupervision
     */
    public function setTeacher(Teacher $teacher): TimetableSupervision {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @param string $location
     * @return TimetableSupervision
     */
    public function setLocation(string $location): TimetableSupervision {
        $this->location = $location;
        return $this;
    }

    public static function fromEntity(TimetableSupervisionEntity $entity): self {
        return (new static())
            ->setUuid($entity->getUuid())
            ->setDay($entity->getDay())
            ->setWeek(TimetableWeek::fromEntity($entity->getWeek()))
            ->setLesson($entity->getLesson())
            ->setIsBefore($entity->isBefore())
            ->setLocation($entity->getLocation())
            ->setTeacher(Teacher::fromEntity($entity->getTeacher()));
    }
}