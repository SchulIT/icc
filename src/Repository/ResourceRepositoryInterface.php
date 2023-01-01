<?php

namespace App\Repository;

use App\Entity\ResourceEntity;
use App\Entity\Room;
use App\Rooms\RoomQuery;

interface ResourceRepositoryInterface {

    public function findOneByName(string $name): ?ResourceEntity;

    public function findOneByUuid(string $uuid): ?ResourceEntity;

    /**
     * @return ResourceEntity[]
     */
    public function findAllNonRooms(): array;

    /**
     * @param RoomQuery $query
     * @return ResourceEntity[]
     */
    public function findAllByQuery(RoomQuery $query): array;

    /**
     * @return ResourceEntity[]
     */
    public function findAll(): array;

    public function persist(ResourceEntity $resource): void;

    public function remove(ResourceEntity $resource): void;
}