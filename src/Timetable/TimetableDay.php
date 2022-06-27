<?php

namespace App\Timetable;

use App\Entity\TimetableSupervision;
use App\Entity\TimetableLesson as TimetableLessonEntity;
use DateTime;

class TimetableDay {

    private DateTime $date;

    private bool $isCurrentDay;

    /** @var bool Flag whether this is the upcoming day when viewing timetable on weekends */
    private bool $isUpcomingDay;

    /** @var bool Flag whether this day is considered free */
    private bool $isFree = false;

    /**
     * @var TimetableLessonContainer[]
     */
    private array $lessons = [ ];

    public function __construct(DateTime $date, bool $isCurrentDay, bool $isUpcomingDay, bool $isFree) {
        $this->date = $date;
        $this->isCurrentDay = $isCurrentDay;
        $this->isUpcomingDay = $isUpcomingDay;
        $this->isFree = $isFree;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
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
     * @return TimetableLessonContainer[]
     */
    public function getLessonsContainers(): array {
        return $this->lessons;
    }

    /**
     * @param int $lesson
     * @return TimetableLessonContainer
     */
    public function getTimetableLessonsContainer(int $lesson): TimetableLessonContainer {
        if(!array_key_exists($lesson, $this->lessons)) {
            $this->lessons[$lesson] = new TimetableLessonContainer($lesson);
        }

        return $this->lessons[$lesson];
    }

    /**
     * @param int $lesson
     */
    public function addEmptyTimetableLessonsContainer(int $lesson): void {
        if(!array_key_exists($lesson, $this->lessons)) {
            $this->lessons[$lesson] = new TimetableLessonContainer($lesson);
        }
    }

    /**
     * @param TimetableLessonEntity $lessonEntity
     */
    public function addTimetableLessonsContainer(TimetableLessonEntity $lessonEntity): void {
        for($lessonNumber = $lessonEntity->getLessonStart(); $lessonNumber <= $lessonEntity->getLessonEnd(); $lessonNumber++) {
            $container = $this->getTimetableLessonsContainer($lessonNumber);
            $container->addTimetableLesson($lessonEntity);
        }
    }

    /**
     * @param TimetableSupervision $supervision
     */
    public function addSupervisionEntry(TimetableSupervision $supervision): void {
        $container = $this->getTimetableLessonsContainer($supervision->getLesson());

        if($supervision->isBefore()) {
            $container->addBeforeSupervison($supervision);
        } else {
            $container->addSupervision($supervision);
        }
    }
}