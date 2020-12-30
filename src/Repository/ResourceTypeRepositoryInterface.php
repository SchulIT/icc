<?php

namespace App\Repository;

use App\Entity\ResourceType;

interface ResourceTypeRepositoryInterface {

    /**
     * Returns the resource type for rooms (which is created by default and cannot be removed)
     *
     * @return ResourceType
     */
    public function findRoomType(): ResourceType;

    /**
     * @return ResourceType[]
     */
    public function findAll(): array;

    public function persist(ResourceType $type): void;

    public function remove(ResourceType $type): void;
}