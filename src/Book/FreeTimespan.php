<?php

namespace App\Book;

use DateTime;

class FreeTimespan {

    /** @var DateTime */
    private $date;

    /** @var int */
    private $lessonStart;

    /** @var int */
    private $lessonEnd;

    /** @var string */
    private $reason;

    public function __construct(DateTime $date, int $lessonStart, int $lessonEnd, string $reason) {
        $this->date = $date;
        $this->lessonStart = $lessonStart;
        $this->lessonEnd = $lessonEnd;
        $this->reason = $reason;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @return string
     */
    public function getReason(): string {
        return $this->reason;
    }
}