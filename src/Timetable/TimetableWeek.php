<?php

namespace App\Timetable;

use DateTime;

class TimetableWeek {

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

    public function __construct(private int $year, private int $week, private ?string $label)
    {
    }

    public function getYear(): int {
        return $this->year;
    }

    public function getWeek(): int {
        return $this->week;
    }

    /**
     * @return string
     */
    public function getLabel(): ?string {
        return $this->label;
    }

    public function setMaxLesson(int $maxLessons): TimetableWeek {
        $this->maxLessons = $maxLessons;
        return $this;
    }

    public function getMaxLessons(): int {
        return $this->maxLessons;
    }

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

    public function getStartDate(): DateTime {
        $dateTime = new DateTime();
        $dateTime->setISODate($this->getYear(), $this->getWeek());
        $dateTime->setTime(0,0,0);

        return $dateTime;
    }

    public function getEndDate(): DateTime {
        $dateTime = $this->getStartDate();
        $dateTime->modify('+6 days');

        return $dateTime;
    }

    public function hasSupervisionAfterMaxLesson(): bool {
        foreach($this->days as $day) {
            if($day->getTimetableLessonsContainer($this->getMaxLessons() + 1)->hasSupervisionBefore()) {
                return true;
            }
        }

        return false;
    }
}