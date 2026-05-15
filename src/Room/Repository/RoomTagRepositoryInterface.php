<?php

namespace App\Room\Repository;

use App\Common\Entity\RoomTag;

interface RoomTagRepositoryInterface {

    /**
     * @return RoomTag[]
     */
    public function findAll(): array;

    public function persist(RoomTag $roomTag): void;

    public function remove(RoomTag $roomTag): void;
}