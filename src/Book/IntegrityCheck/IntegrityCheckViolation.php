<?php

namespace App\Book\IntegrityCheck;

use App\Entity\TimetableLesson;
use DateTime;

class IntegrityCheckViolation {
    public function __construct(private readonly DateTime $date, private readonly int $lesson, private readonly ?TimetableLesson $timetableLesson, private readonly string $message) { }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function getTimetableLesson(): ?TimetableLesson {
        return $this->timetableLesson;
    }

    public function getMessage(): string {
        return $this->message;
    }
}