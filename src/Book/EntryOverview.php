<?php

namespace App\Book;

use App\Entity\BookComment;
use App\Grouping\LessonDayGroup;
use DateTime;

class EntryOverview {

    /** @var DateTime */
    private DateTime $start;

    /** @var DateTime */
    private DateTime $end;

    /** @var LessonDayGroup[] */
    private array $days;

    /** @var BookComment[] */
    private array $comments;

    /** @var FreeTimespan[] */
    private array $freeTimespans;

    public function __construct(DateTime $start, DateTime $end, array $days, array $comments, array $freeTimespans) {
        $this->start = $start;
        $this->end = $end;
        $this->days = $days;
        $this->comments = $comments;
        $this->freeTimespans = $freeTimespans;
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

    public function hasLessonsWithinFreeTimespans(DateTime $dateTime): bool {
        $freeTimespans = $this->getFreeTimespans($dateTime);

        foreach($this->days as $day) {
            if($day->getDate() == $dateTime) {
                foreach($day->getLessons() as $lesson) {
                    if($lesson->getEntry() === null) {
                        foreach($freeTimespans as $timespan) {
                            if($timespan->getLessonStart() <= $lesson->getLessonNumber() && $lesson->getLessonNumber() <= $timespan->getLessonEnd()) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param DateTime $dateTime
     * @return FreeTimespan[]
     */
    public function getFreeTimespans(DateTime $dateTime): array {
        return array_filter($this->freeTimespans, function(FreeTimespan $timespan) use($dateTime) {
            return $timespan->getDate() == $dateTime;
        });
    }
}