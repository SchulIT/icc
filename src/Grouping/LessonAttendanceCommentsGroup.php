<?php

namespace App\Grouping;

use App\Book\Student\LessonAttendance;
use App\Entity\BookComment;
use DateTime;

class LessonAttendanceCommentsGroup implements GroupInterface, SortableGroupInterface {

    /** @var LessonAttendance[] */
    private array $attendances = [ ];

    /** @var BookComment[] */
    private array $comments = [ ];

    public function __construct(private DateTime $date)
    {
    }

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