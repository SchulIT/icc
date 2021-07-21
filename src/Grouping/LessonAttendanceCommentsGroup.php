<?php

namespace App\Grouping;

use App\Book\Student\LessonAttendance;
use App\Entity\BookComment;
use DateTime;

class LessonAttendanceCommentsGroup implements GroupInterface, SortableGroupInterface {

    /** @var DateTime */
    private $date;

    /** @var LessonAttendance[] */
    private $attendances = [ ];

    /** @var BookComment[] */
    private $comments = [ ];

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

    /**
     * @return BookComment[]
     */
    public function getComments(): array {
        return $this->comments;
    }

    public function addItem($item) {
        if($item instanceof BookComment) {
            $this->comments[] = $item;
        } else {
            $this->attendances[] = $item;
        }
    }

    public function getKey() {
        return $this->date;
    }

    public function &getItems(): array {
        return $this->attendances;
    }
}