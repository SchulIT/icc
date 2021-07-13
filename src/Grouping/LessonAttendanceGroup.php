<?php

namespace App\Grouping;

use App\Book\Student\LessonAttendance;
use DateTime;

class LessonAttendanceGroup implements GroupInterface, SortableGroupInterface {

    /** @var DateTime */
    private $date;

    /** @var LessonAttendance[] */
    private $attendances = [ ];

    public function __construct(DateTime $date) {
        $this->date = $date;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return LessonAttendance[]
     */
    public function getAttendances(): array {
        return $this->attendances;
    }

    public function addItem($item) {
        $this->attendances[] = $item;
    }

    public function getKey() {
        return $this->date;
    }

    public function &getItems(): array {
        return $this->attendances;
    }
}