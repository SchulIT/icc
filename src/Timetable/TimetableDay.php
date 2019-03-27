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

    /**
     * @var TimetableLesson[]
     */
    private $lessons = [ ];

    public function __construct(int $day, bool $isCurrentDay) {
        $this->day = $day;
        $this->isCurrentDay = $isCurrentDay;
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
     * @param TimetableLesson $lesson
     */
    public function addLesson(TimetableLesson $lesson): void {
        $this->lessons[] = $lesson;
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
     * @param TimetableLessonEntity $lessonEntity
     */
    public function addTimetableLesson(TimetableLessonEntity $lessonEntity): void {
        $lesson = $this->getTimetableLesson($lessonEntity->getLesson());
        $lesson->addTimetableLesson($lessonEntity);
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