<?php

namespace App\Import;

use App\Repository\TransactionalRepositoryInterface;

interface ImportStrategyInterface {

    /**
     * Returns the entities which are to be imported.
     *
     * @param object $data The data object of the request body.
     * @return array
     */
    public function getData($data): array;

    /**
     * Returns all existing entities
     *
     * @return array
     */
    public function getExistingEntities(): array;

    /**
     * Creates a new entity based on the given data (which is imported from JSON)
     *
     * @param mixed $data
     * @return mixed
     */
    public function createNewEntity($data);

    /**
     * Returns an existing entity based on the imported data or null if such entity does not exist
     *
     * @param mixed $object
     * @param array $existingEntities
     * @return mixed|null
     */
    public function getExistingEntity($object, array $existingEntities);

    /**
     * Returns the entities ID
     *
     * @param mixed $entity
     * @return int
     */
    public function getEntityId($entity): int;

    /**
     * Updates an existing entity based on the imported data (from JSON)
     *
     * @param mixed $entity
     * @param mixed $data
     */
    public function updateEntity($entity, $data): void;

    /**
     * Persists an entity to the database
     *
     * @param mixed $entity
     */
    public function persist($entity): void;

    /**
     * Removes an entity from the database
     *
     * @param mixed $entity
     */
    public function remove($entity): void;

    /**
     * Returns the repository used for import
     *
     * @return TransactionalRepositoryInterface
     */
    public function getRepository(): TransactionalRepositoryInterface;
}