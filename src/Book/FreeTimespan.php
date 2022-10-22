<?php

namespace App\Book;

use DateTime;

class FreeTimespan {

    public function __construct(private DateTime $date, private int $lessonStart, private int $lessonEnd, private string $reason)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function getReason(): string {
        return $this->reason;
    }
}