<?php

namespace App\ParentsDay\Repository;

use App\ParentsDay\Entity\ParentsDay;
use App\ParentsDay\Entity\ParentsDayTeacherRoom;
use App\Common\Entity\Room;
use App\Common\Entity\Teacher;
use App\Framework\Repository\TransactionalRepositoryInterface;

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