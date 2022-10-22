<?php

namespace App\Timetable;

use App\Entity\TimetableSupervision;
use App\Entity\TimetableLesson as TimetableLessonEntity;
use DateTime;

class TimetableDay {

    /**
     * @var TimetableLessonContainer[]
     */
    private array $lessons = [ ];

    public function __construct(private DateTime $date, private bool $isCurrentDay, private bool $isUpcomingDay, private bool $isFree)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function isCurrentDay(): bool {
        return $this->isCurrentDay;
    }

    public function isUpcomingDay(): bool {
        return $this->isUpcomingDay;
    }

    public function isFree(): bool {
        return $this->isFree;
    }

    /**
     * @return TimetableLessonContainer[]
     */
    public function getLessonsContainers(): array {
        return $this->lessons;
    }

    public function getTimetableLessonsContainer(int $lesson): TimetableLessonContainer {
        if(!array_key_exists($lesson, $this->lessons)) {
            $this->lessons[$lesson] = new TimetableLessonContainer($lesson);
        }

        return $this->lessons[$lesson];
    }

    public function addEmptyTimetableLessonsContainer(int $lesson): void {
        if(!array_key_exists($lesson, $this->lessons)) {
            $this->lessons[$lesson] = new TimetableLessonContainer($lesson);
        }
    }

    public function addTimetableLessonsContainer(TimetableLessonEntity $lessonEntity): void {
        for($lessonNumber = $lessonEntity->getLessonStart(); $lessonNumber <= $lessonEntity->getLessonEnd(); $lessonNumber++) {
            $container = $this->getTimetableLessonsContainer($lessonNumber);
            $container->addTimetableLesson($lessonEntity);
        }
    }

    public function addSupervisionEntry(TimetableSupervision $supervision): void {
        $container = $this->getTimetableLessonsContainer($supervision->getLesson());

        if($supervision->isBefore()) {
            $container->addBeforeSupervison($supervision);
        } else {
            $container->addSupervision($supervision);
        }
    }
}