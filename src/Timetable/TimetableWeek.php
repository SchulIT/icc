<?php

namespace App\Timetable;

use App\Entity\TimetableWeek as TimetableWeekEntity;

class TimetableWeek {

    /** @var TimetableWeekEntity */
    private $week;

    /**
     * Maximum of lesson number of all days in a week
     * @var int
     */
    public $maxLessons;

    /** @var TimetableDay[] */
    public $days = [ ];

    /**
     * @var bool
     */
    public $isCurrentOrUpcoming = false;

    public function __construct(TimetableWeekEntity $week) {
        $this->week = $week;
    }

    /**
     * @return string
     */
    public function getWeekName(): string {
        return $this->week->getDisplayName();
    }

    /**
     * @return int
     */
    public function getWeekMod(): int {
        return $this->week->getWeekMod();
    }

    public function getWeek(): TimetableWeekEntity {
        return $this->week;
    }

    /**
     * @param int $maxLessons
     * @return TimetableWeek
     */
    public function setMaxLesson(int $maxLessons): TimetableWeek {
        $this->maxLessons = $maxLessons;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLessons(): int {
        return $this->maxLessons;
    }

    /**
     * @param int $firstLesson
     * @return bool
     */
    public function hasSupervisionBefore(int $firstLesson): bool {
        foreach($this->days as $day) {
            $ttlFirst = $day->getTimetableLesson($firstLesson);

            if($ttlFirst->hasSupervisionBefore()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $firstLesson
     * @param int $secondLesson
     * @return bool
     */
    public function areCombinedLessons(int $firstLesson, int $secondLesson): bool {
        foreach($this->days as $day) {
            $ttlFirst = $day->getTimetableLesson($firstLesson);
            $ttlSecond = $day->getTimetableLesson($secondLesson);

            if($ttlFirst->includeNextLesson() === false || $ttlSecond->isCollapsed() === false) {
                return false;
            }
        }

        return true;
    }

    public function setCurrentOrUpcoming(): void {
        $this->isCurrentOrUpcoming = true;
    }

    public function isCurrentOrUpcoming(): bool {
        return $this->isCurrentOrUpcoming;
    }
}