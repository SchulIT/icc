<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayTeacherRoom;
use App\Entity\Room;
use App\Entity\Teacher;

interface ParentsDayTeacherRoomRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param Teacher $teacher
     * @param ParentsDay $parentsDay
     * @return Room|null
     */
    public function findRoomByTeacherAndParentsDay(Teacher $teacher, ParentsDay $parentsDay): ?Room;

    /**
     * @param ParentsDay $parentsDay
     * @return ParentsDayTeacherRoom[]
     */
    public function findAllByParentsDay(ParentsDay $parentsDay): array;

    public function persist(ParentsDayTeacherRoom $parentsDayTeacherRoom): void;

    public function remove(ParentsDayTeacherRoom $parentsDayTeacherRoom): void;

    public function removeByParentsDay(ParentsDay $parentsDayTeacherRoom): int;
}