<?php

namespace App\Book;

use App\Entity\BookComment;
use App\Grouping\LessonDayGroup;
use DateTime;

class EntryOverview {

    /** @var DateTime */
    private $start;

    /** @var DateTime */
    private $end;

    /** @var LessonDayGroup[] */
    private $days;

    /** @var BookComment[] */
    private $comments;

    public function __construct(DateTime $start, DateTime $end, array $days, array $comments) {
        $this->start = $start;
        $this->end = $end;
        $this->days = $days;
        $this->comments = $comments;
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

    public function getComments(DateTime $dateTime): array {
        return array_filter($this->comments, function(BookComment $comment) use($dateTime) {
            return $comment->getDate() == $dateTime;
        });
    }
}