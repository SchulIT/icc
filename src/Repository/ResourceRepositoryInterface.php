<?php

namespace App\Repository;

use App\Entity\Resource;
use App\Entity\Room;
use App\Rooms\RoomQuery;

interface ResourceRepositoryInterface {

    public function findOneByUuid(string $uuid): ?Resource;

    /**
     * @return Resource[]
     */
    public function findAllNonRooms(): array;

    /**
     * @param RoomQuery $query
     * @return Room[]
     */
    public function findAllByQuery(RoomQuery $query): array;

    /**
     * @return Resource[]
     */
    public function findAll(): array;

    public function persist(Resource $resource): void;

    public function remove(Resource $resource): void;
}