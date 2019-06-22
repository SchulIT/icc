<?php

namespace App\Repository;

use App\Entity\RoomTag;

interface RoomTagRepositoryInterface {

    /**
     * @return RoomTag[]
     */
    public function findAll(): array;

    public function persist(RoomTag $roomTag): void;

    public function remove(RoomTag $roomTag): void;
}