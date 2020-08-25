<?php

namespace App\Rooms\Reservation;

use App\Entity\Room;

class RoomAvailabilityOverview {
    private $maxLessons;

    private $rooms = [ ];

    public function __construct(int $maxLessons) {
        $this->maxLessons = $maxLessons;
    }

    /**
     * @return int
     */
    public function getMaxLessons(): int {
        return $this->maxLessons;
    }

    public function addAvailability(Room $room, int $lessonNumber, RoomAvailability $availability) {
        if(!isset($this->rooms[$room->getId()])) {
            $this->rooms[$room->getId()] = [ ];
        }

        $this->rooms[$room->getId()][$lessonNumber] = $availability;
    }

    public function getAvailability(Room $room, int $lessonNumber): ?RoomAvailability {
        return $this->rooms[$room->getId()][$lessonNumber] ?? null;
    }
}