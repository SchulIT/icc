<?php

namespace App\Book\IntegrityCheck;

use App\Entity\BookEvent;
use App\Entity\TimetableLesson;
use DateTime;

class IntegrityCheckViolation {
    public function __construct(private readonly DateTime $date, private readonly int $lesson, private readonly TimetableLesson|BookEvent|null $timetableLessonOrEvent, private readonly string $message) { }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function getTimetableLesson(): ?TimetableLesson {
        if ($this->timetableLessonOrEvent instanceof TimetableLesson) {
            return $this->timetableLessonOrEvent;
        }

        return null;
    }

    public function getEvent(): ?BookEvent {
        if($this->timetableLessonOrEvent instanceof BookEvent) {
            return $this->timetableLessonOrEvent;
        }

        return null;
    }

    public function getMessage(): string {
        return $this->message;
    }
}