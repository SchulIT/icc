<?php

namespace App\Repository;

use App\Entity\Room;
use App\Rooms\RoomQuery;

interface RoomRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Room|null
     */
    public function findOneById(int $id): ?Room;

    /**
     * @param string $uuid
     * @return Room|null
     */
    public function findOneByUuid(string $uuid): ?Room;

    /**
     * @param string $externalId
     * @return Room|null
     */
    public function findOneByExternalId(string $externalId): ?Room;

    /**
     * @param RoomQuery $query
     * @return Room[]
     */
    public function findAllByQuery(RoomQuery $query): array;

    /**
     * @return Room[]
     */
    public function findAll(): array;

    public function persist(Room $room): void;

    public function remove(Room $room): void;
}