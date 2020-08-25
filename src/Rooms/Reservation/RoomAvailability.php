<?php

namespace App\Rooms\Reservation;

use App\Entity\RoomReservation;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;

class RoomAvailability {

    private $reservation;

    private $timetableLesson;

    private $substitution;

    private $isTimetableLessonCancelled = false;

    public function __construct(?RoomReservation $reservation, ?TimetableLesson $timetableLesson, ?Substitution $substitution) {
        $this->reservation = $reservation;
        $this->timetableLesson = $timetableLesson;
        $this->substitution = $substitution;
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

    public function setTimetableLessonCancelled(): void {
        $this->isTimetableLessonCancelled = true;
    }

    public function isTimetableLessonCancelled(): bool {
        return $this->isTimetableLessonCancelled;
    }

    public function isAvailable(): bool {
        return $this->reservation === null
            && $this->substitution === null
            && ($this->timetableLesson === null || $this->isTimetableLessonCancelled());
    }
}