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
     * @return Room[]
     */
    public function findAll(): array;

    /**
     * Like findAll() but only returns rooms which were imported (externalId != null)
     *
     * @return Room[]
     */
    public function findAllExternal(): array;

    public function persist(Room $room): void;

    public function remove(Room $room): void;
}