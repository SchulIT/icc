<?php

namespace App\Rooms\Reservation;

use App\Entity\Exam;
use App\Entity\ResourceReservation;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;

class ResourceAvailability {

    private bool $isTimetableLessonCancelled = false;

    public function __construct(private ?ResourceReservation $reservation, private ?TimetableLesson $timetableLesson, private ?Substitution $substitution, private array $exams)
    {
    }

    public function getReservation(): ?ResourceReservation {
        return $this->reservation;
    }

    public function getTimetableLesson(): ?TimetableLesson {
        return $this->timetableLesson;
    }

    public function getSubstitution(): ?Substitution {
        return $this->substitution;
    }

    /**
     * @return Exam[]
     */
    public function getExams(): array {
        return $this->exams;
    }

    public function setTimetableLessonCancelled(): void {
        $this->isTimetableLessonCancelled = true;
    }

    public function isTimetableLessonCancelled(): bool {
        return $this->isTimetableLessonCancelled;
    }

    public function isAvailable(): bool {
        return $this->reservation === null
            && $this->substitution === null
            && count($this->exams) === 0
            && ($this->timetableLesson === null || $this->isTimetableLessonCancelled());
    }
}