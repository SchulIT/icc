<?php

namespace App\Timetable;

class TimetableWeek {

    /** @var string */
    private $weekType;

    /** @var int */
    public $weekMod;

    /**
     * Maximum of lesson number of all days in a week
     * @var int
     */
    public $maxLessons;

    /** @var TimetableDay[] */
    public $days = [ ];

    public function __construct(string $weekType, int $weekMod) {
        $this->weekType = $weekType;
        $this->weekMod = $weekMod;
    }

    /**
     * @return string
     */
    public function getWeekType(): string {
        return $this->weekType;
    }

    /**
     * @return int
     */
    public function getWeekMod(): int {
        return $this->weekMod;
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
}