<?php

namespace App\Import;

use App\Repository\TransactionalRepositoryInterface;

interface ReplaceImportStrategyInterface {

    /**
     * Returns the class name of the entity which is imported in order to
     * save the timestamp of the last import.
     *
     * @return string
     */
    public function getEntityClassName(): string;

    /**
     * Returns the entities which are to be imported.
     *
     * @param object $data The data object of the request body.
     * @return array
     */
    public function getData($data): array;

    public function getRepository(): TransactionalRepositoryInterface;

    /**
     * @param object $data The data object of the request body.
     */
    public function removeAll($data): void;

    /**
     * @param object $data
     * @param object $requestData
     * @throws EntityIgnoredException
     */
    public function persist($data, $requestData): void;
}