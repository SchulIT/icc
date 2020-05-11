<?php

namespace App\Rooms\Reservation;

use App\Entity\Room;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use DateTime;

class RoomAvailabilityHelper {
    private $substitutionRepository;
    private $timetableRepository;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, TimetableLessonRepositoryInterface $timetableRepository) {
        $this->substitutionRepository = $substitutionRepository;
        $this->timetableRepository = $timetableRepository;
    }

    public function getAvailability(Room $room, DateTime $dateTime, int $lessonStart, int $lessonEnd) {
        /** @var RoomAvailability[] $availabilities */
        $availabilities = [ ];



        return $availabilities;
    }
}