<?php

namespace App\Response\Api\V1;

use App\Entity\TimetableLesson as TimetableLessonEntity;
use JMS\Serializer\Annotation as Serializer;

class TimetableLesson {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("tuition")
     * @Serializer\Type("App\Response\Api\V1\Tuition")
     * @var Tuition
     */
    private $tuition;

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
     * @Serializer\SerializedName("double_lesson")
     * @Serializer\Type("bool")
     * @var bool
     */
    private $isDoubleLesson;

    /**
     * @Serializer\SerializedName("room")
     * @Serializer\Type("App\Response\Api\V1\Room")
     * @var Room
     */
    private $room;

    /**
     * @return Tuition
     */
    public function getTuition(): Tuition {
        return $this->tuition;
    }

    /**
     * @param Tuition $tuition
     * @return TimetableLesson
     */
    public function setTuition(Tuition $tuition): TimetableLesson {
        $this->tuition = $tuition;
        return $this;
    }

    /**
     * @return TimetableWeek
     */
    public function getWeek(): TimetableWeek {
        return $this->week;
    }

    /**
     * @param TimetableWeek $week
     * @return TimetableLesson
     */
    public function setWeek(TimetableWeek $week): TimetableLesson {
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
     * @return TimetableLesson
     */
    public function setDay(int $day): TimetableLesson {
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
     * @return TimetableLesson
     */
    public function setLesson(int $lesson): TimetableLesson {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDoubleLesson(): bool {
        return $this->isDoubleLesson;
    }

    /**
     * @param bool $isDoubleLesson
     * @return TimetableLesson
     */
    public function setIsDoubleLesson(bool $isDoubleLesson): TimetableLesson {
        $this->isDoubleLesson = $isDoubleLesson;
        return $this;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room {
        return $this->room;
    }

    /**
     * @param Room $room
     * @return TimetableLesson
     */
    public function setRoom(Room $room): TimetableLesson {
        $this->room = $room;
        return $this;
    }

    public static function fromEntity(TimetableLessonEntity $entity): self {
        return (new static())
            ->setUuid($entity->getUuid())
            ->setTuition(Tuition::fromEntity($entity->getTuition()))
            ->setWeek(TimetableWeek::fromEntity($entity->getWeek()))
            ->setDay($entity->getDay())
            ->setIsDoubleLesson($entity->isDoubleLesson())
            ->setRoom(Room::fromEntity($entity->getRoom()));
    }
}