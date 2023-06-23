<?php

namespace App\Repository;

use App\Entity\ResourceEntity;
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
     * @param ResourceEntity|null $resource
     * @param Teacher|null $teacher
     * @param DateTime|null $from
     * @return ResourceReservation[]
     */
    public function findAllByRoomAndTeacher(?ResourceEntity $resource, ?Teacher $teacher, ?DateTime $from): array;

    /**
     * @param Teacher $teacher
     * @param DateTime $date
     * @return ResourceReservation[]
     */
    public function findAllByTeacherAndDate(Teacher $teacher, DateTime $date): array;

    /**
     * @param DateTime $dateTime
     * @param ResourceEntity $resource
     * @param int $lessonNumber
     * @return ResourceReservation|null
     */
    public function findOneByDateAndResourceAndLesson(DateTime $dateTime, ResourceEntity $resource, int $lessonNumber): ?ResourceReservation;

    /**
     * @param DateTime $date
     * @return ResourceReservation[]
     */
    public function findAllByDate(DateTime $date): array;

    /**
     * @param ResourceEntity $resource
     * @param DateTime $dateTime
     * @return ResourceReservation[]
     */
    public function findAllByResourceAndDate(ResourceEntity $resource, DateTime $dateTime): array;

    public function persist(ResourceReservation $reservation): void;

    public function remove(ResourceReservation $reservation): void;

    public function removeBetween(DateTime $start, DateTime $end): int;
}