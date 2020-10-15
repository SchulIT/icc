<?php

namespace App\Rooms\Reservation;

use App\Entity\Exam;
use App\Entity\RoomReservation;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;

class RoomAvailability {

    private $reservation;

    private $timetableLesson;

    private $substitution;

    private $exams = [ ];

    private $isTimetableLessonCancelled = false;

    public function __construct(?RoomReservation $reservation, ?TimetableLesson $timetableLesson, ?Substitution $substitution, array $exams) {
        $this->reservation = $reservation;
        $this->timetableLesson = $timetableLesson;
        $this->substitution = $substitution;
        $this->exams = $exams;
    }

    /**
     * @return RoomReservation|null
     */
    public function getReservation(): ?RoomReservation {
        return $this->reservation;
    }

    /**
     * @return TimetableLesson|null
     */
    public function getTimetableLesson(): ?TimetableLesson {
        return $this->timetableLesson;
    }

    /**
     * @return Substitution|null
     */
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