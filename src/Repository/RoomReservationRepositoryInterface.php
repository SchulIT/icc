<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\RoomReservation;
use App\Entity\Teacher;
use DateTime;

interface RoomReservationRepositoryInterface {

    /**
     * @return RoomReservation[]
     */
    public function findAll(): array;

    /**
     * @param Room|null $room
     * @param Teacher|null $teacher
     * @param DateTime|null $from
     * @return RoomReservation[]
     */
    public function findAllByRoomAndTeacher(?Room $room, ?Teacher $teacher, ?DateTime $from): array;

    /**
     * @param DateTime $dateTime
     * @param Room $room
     * @param int $lessonNumber
     * @return RoomReservation|null
     */
    public function findOneByDateAndRoomAndLesson(DateTime $dateTime, Room $room, int $lessonNumber): ?RoomReservation;

    /**
     * @param DateTime $date
     * @return RoomReservation[]
     */
    public function findAllByDate(DateTime $date): array;

    public function persist(RoomReservation $reservation): void;

    public function remove(RoomReservation $reservation): void;
}