<?php

namespace App\Response\Api\V1;

use App\Entity\FreestyleTimetableLesson;
use App\Entity\Teacher as TeacherEntity;
use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TuitionTimetableLesson;
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
     * Contains the list of teachers (which is the same as tuition.teachers in case tuition is not null)
     *
     * @Serializer\SerializedName("teachers")
     * @var Teacher[]
     */
    private $teachers;

    /**
     * Contains the subject as a string (which is the same as tuition.subject.name in case tuition is not null)
     *
     * @Serializer\SerializedName("subject")
     * @var string
     */
    private $subject;

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

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param Teacher[] $teachers
     * @return TimetableLesson
     */
    public function setTeachers(array $teachers): TimetableLesson {
        $this->teachers = $teachers;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return TimetableLesson
     */
    public function setSubject(string $subject): TimetableLesson {
        $this->subject = $subject;
        return $this;
    }

    public static function fromEntity(TimetableLessonEntity $entity): self {
        $lesson = (new self())
            ->setUuid($entity->getUuid())
            ->setWeek(TimetableWeek::fromEntity($entity->getWeek()))
            ->setDay($entity->getDay())
            ->setIsDoubleLesson($entity->isDoubleLesson());

        if($entity instanceof TuitionTimetableLesson) {
            $lesson
                ->setTuition(Tuition::fromEntity($entity->getTuition()))
                ->setRoom(Room::fromEntity($entity->getRoom()));

            $lesson->setSubject($lesson->getTuition()->getSubject()->getAbbreviation());
            $lesson->setTeachers($lesson->getTuition()->getTeachers());
        } elseif($entity instanceof FreestyleTimetableLesson) {
            $lesson->setTeachers(array_map(function(TeacherEntity $entity) {
                return Teacher::fromEntity($entity);
            }, $entity->getTeachers()->toArray()))
                ->setSubject($entity->getSubject());
        }

        return $lesson;
    }
}