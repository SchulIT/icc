<?php

namespace App\Book\Grouping;

use App\Book\Entity\BookComment;
use App\Book\Student\LessonAttendance;
use App\Framework\Grouping\SortableGroupInterface;
use DateTime;

/**
 * @implements SortableGroupInterface<DateTime, LessonAttendance|BookComment>
 */
class LessonAttendanceCommentsGroup implements SortableGroupInterface {

    /** @var LessonAttendance[] */
    private array $attendances = [ ];

    /** @var BookComment[] */
    private array $comments = [ ];

    public function __construct(private readonly DateTime $date)
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

    public function addItem($item): void {
        if($item instanceof BookComment) {
            $this->comments[] = $item;
        } else {
            $this->attendances[] = $item;
        }
    }

    public function getKey(): DateTime {
        return $this->date;
    }

    public function &getItems(): array {
        return $this->attendances;
    }
}