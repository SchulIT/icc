<?php

namespace App\Rooms\Reservation;

use App\Entity\RoomReservation;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;

class RoomAvailability {

    private $lesson;

    private $reservation;

    private $timetableLesson;

    private $substitution;

    public function __construct(int $lesson, ?RoomReservation $reservation, ?TimetableLesson $timetableLesson, ?Substitution $substitution) {
        $this->lesson = $lesson;
        $this->reservation = $reservation;
        $this->timetableLesson = $timetableLesson;
        $this->substitution = $substitution;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
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

    public function isAvailable(): bool {
        return $this->reservation === null
            && $this->timetableLesson === null
            && $this->substitution === null;
    }
}