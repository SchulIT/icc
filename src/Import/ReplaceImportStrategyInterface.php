<?php

namespace App\Import;

use App\Repository\TransactionalRepositoryInterface;

interface ReplaceImportStrategyInterface {

    /**
     * Returns the entities which are to be imported.
     *
     * @param object The data object of the request body.
     * @return array
     */
    public function getData($data): array;

    public function getRepository(): TransactionalRepositoryInterface;

    public function removeAll(): void;

    public function persist($data): void;
}