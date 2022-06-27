<?php

namespace App\Timetable;

class TimetableWeek {

    private int $year;

    private int $week;

    private ?string $label;

    /**
     * Maximum of lesson number of all days in a week
     * @var int
     */
    public int $maxLessons;

    /** @var TimetableDay[] */
    public array $days = [ ];

    /**
     * @var bool
     */
    public bool $isCurrentOrUpcoming = false;

    public function __construct(int $year, int $week, ?string $label) {
        $this->year = $year;
        $this->week = $week;
        $this->label = $label;
    }

    /**
     * @return int
     */
    public function getYear(): int {
        return $this->year;
    }

    /**
     * @return int
     */
    public function getWeek(): int {
        return $this->week;
    }

    /**
     * @return string
     */
    public function getLabel(): ?string {
        return $this->label;
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
            $ttlFirst = $day->getTimetableLessonsContainer($firstLesson);

            if($ttlFirst->hasSupervisionBefore()) {
                return true;
            }
        }

        return false;
    }

    public function setCurrentOrUpcoming(): void {
        $this->isCurrentOrUpcoming = true;
    }

    public function isCurrentOrUpcoming(): bool {
        return $this->isCurrentOrUpcoming;
    }
}