<?php

namespace App\Timetable;

use App\Entity\TimetableSupervision;
use App\Entity\TimetableLesson as TimetableLessonEntity;

class TimetableDay {
    /**
     * @var int
     */
    private $day;

    /**
     * @var bool
     */
    private $isCurrentDay;

    /** @var bool Flag whether this is the upcoming day when viewing timetable on weekends */
    private $isUpcomingDay;

    /** @var bool Flag whether this day is considered free */
    private $isFree = false;

    /**
     * @var TimetableLesson[]
     */
    private $lessons = [ ];

    public function __construct(int $day, bool $isCurrentDay, bool $isUpcomingDay, bool $isFree) {
        $this->day = $day;
        $this->isCurrentDay = $isCurrentDay;
        $this->isUpcomingDay = $isUpcomingDay;
        $this->isFree = $isFree;
    }

    /**
     * @return int
     */
    public function getDay(): int {
        return $this->day;
    }

    /**
     * @return bool
     */
    public function isCurrentDay(): bool {
        return $this->isCurrentDay;
    }

    /**
     * @return bool
     */
    public function isUpcomingDay(): bool {
        return $this->isUpcomingDay;
    }

    /**
     * @return bool
     */
    public function isFree(): bool {
        return $this->isFree;
    }

    /**
     * @return TimetableLesson[]
     */
    public function getLessons() {
        return $this->lessons;
    }

    /**
     * @param int $lesson
     * @return TimetableLesson
     */
    public function getTimetableLesson(int $lesson): TimetableLesson {
        if(!array_key_exists($lesson, $this->lessons)) {
            $this->lessons[$lesson] = new TimetableLesson($lesson);
        }

        return $this->lessons[$lesson];
    }

    /**
     * @param int $lesson
     */
    public function addEmptyTimetableLesson(int $lesson): void {
        if(!array_key_exists($lesson, $this->lessons)) {
            $this->lessons[$lesson] = new TimetableLesson($lesson);
        }
    }

    /**
     * @param TimetableLessonEntity $lessonEntity
     */
    public function addTimetableLesson(TimetableLessonEntity $lessonEntity): void {
        $lesson = $this->getTimetableLesson($lessonEntity->getLesson());
        $lesson->addTimetableLesson($lessonEntity);

        if($lessonEntity->isDoubleLesson()) {
            $nextLesson = $this->getTimetableLesson($lessonEntity->getLesson() + 1);
            $nextLesson->addTimetableLesson($lessonEntity);
        }
    }

    /**
     * @param TimetableSupervision $supervision
     */
    public function addSupervisionEntry(TimetableSupervision $supervision): void {
        $ttl = $this->getTimetableLesson($supervision->getLesson());

        if($supervision->isBefore()) {
            $ttl->addBeforeSupervison($supervision);
        } else {
            $ttl->addSupervision($supervision);
        }
    }
}