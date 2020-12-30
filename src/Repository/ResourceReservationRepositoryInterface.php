<?php

namespace App\Repository;

use App\Entity\Resource;
use App\Entity\Room;
use App\Entity\ResourceReservation;
use App\Entity\Teacher;
use DateTime;

interface ResourceReservationRepositoryInterface {

    /**
     * @return ResourceReservation[]
     */
    public function findAll(): array;

    /**
     * @param Resource|null $resource
     * @param Teacher|null $teacher
     * @param DateTime|null $from
     * @return ResourceReservation[]
     */
    public function findAllByRoomAndTeacher(?Resource $resource, ?Teacher $teacher, ?DateTime $from): array;

    /**
     * @param Teacher $teacher
     * @param DateTime $date
     * @return ResourceReservation[]
     */
    public function findAllByTeacherAndDate(Teacher $teacher, DateTime $date): array;

    /**
     * @param DateTime $dateTime
     * @param Resource $resource
     * @param int $lessonNumber
     * @return ResourceReservation|null
     */
    public function findOneByDateAndResourceAndLesson(DateTime $dateTime, Resource $resource, int $lessonNumber): ?ResourceReservation;

    /**
     * @param DateTime $date
     * @return ResourceReservation[]
     */
    public function findAllByDate(DateTime $date): array;

    /**
     * @param Resource $resource
     * @param DateTime $dateTime
     * @return ResourceReservation[]
     */
    public function findAllByResourceAndDate(Resource $resource, DateTime $dateTime): array;

    public function persist(ResourceReservation $reservation): void;

    public function remove(ResourceReservation $reservation): void;
}