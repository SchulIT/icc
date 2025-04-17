<?php

namespace App\Repository;

use App\Entity\ReturnItemType;

interface ReturnItemTypeRepositoryInterface {

    /**
     * @return ReturnItemType[]
     */
    public function findAll(): array;

    public function countReturnsForType(ReturnItemType $type): int;

    public function persist(ReturnItemType $returnItemType): void;

    public function remove(ReturnItemType $returnItemType): void;
}