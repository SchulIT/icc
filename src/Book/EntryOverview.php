<?php

namespace App\Book;

use App\Entity\BookComment;
use App\Grouping\LessonDayGroup;
use DateTime;

class EntryOverview {

    /**
     * @param LessonDayGroup[] $days
     * @param BookComment[] $comments
     * @param FreeTimespan[] $freeTimespans
     */
    public function __construct(private DateTime $start, private DateTime $end, private array $days, private array $comments, private array $freeTimespans)
    {
    }

    public function getStart(): DateTime {
        return $this->start;
    }

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
        return array_filter($this->comments, fn(BookComment $comment) => $comment->getDate() == $dateTime);
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
     * @return FreeTimespan[]
     */
    public function getFreeTimespans(DateTime $dateTime): array {
        return array_filter($this->freeTimespans, fn(FreeTimespan $timespan) => $timespan->getDate() == $dateTime);
    }
}