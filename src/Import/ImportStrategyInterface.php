<?php

namespace App\Import;

use App\Repository\TransactionalRepositoryInterface;

interface ImportStrategyInterface {

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
     * @param object $requestData The data object of the request body.
     * @return array
     */
    public function getData($requestData): array;

    /**
     * Returns all existing entities
     *
     * @param object $requestData The data object of the request body.
     * @return array
     */
    public function getExistingEntities($requestData): array;

    /**
     * Creates a new entity based on the given data (which is imported from JSON)
     *
     * @param mixed $data
     * @param object $requestData The data object of the request body.
     * @return mixed
     */
    public function createNewEntity($data, $requestData);

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
     * @param mixed $requestData The data object of the request body.
     * @throws EntityIgnoredException
     */
    public function updateEntity($entity, $data, $requestData): void;

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
     * @param mixed $requestData
     * @return bool Whether or not the item was really removed (true) or just updated (false)
     */
    public function remove($entity, $requestData): bool;

    /**
     * Returns the repository used for import
     *
     * @return TransactionalRepositoryInterface
     */
    public function getRepository(): TransactionalRepositoryInterface;
}