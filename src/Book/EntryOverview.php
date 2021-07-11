<?php

namespace App\Book;

use App\Grouping\LessonDayGroup;
use DateTime;

class EntryOverview {

    /** @var DateTime */
    private $start;

    /** @var DateTime */
    private $end;

    /** @var LessonDayGroup[] */
    private $days;

    public function __construct(DateTime $start, DateTime $end, array $days) {
        $this->start = $start;
        $this->end = $end;
        $this->days = $days;
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime {
        return $this->start;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime {
        return $this->end;
    }

    /**
     * @return LessonDayGroup[]
     */
    public function getDays(): array {
        return $this->days;
    }
}