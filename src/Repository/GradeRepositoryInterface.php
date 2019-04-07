<?php

namespace App\Repository;

use App\Entity\Grade;

interface GradeRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Grade|null
     */
    public function findOneById(int $id): ?Grade;

    /**
     * @param string $name
     * @return Grade|null
     */
    public function findOneByName(string $name): ?Grade;

    /**
     * @return Grade[]
     */
    public function findAll();

    /**
     * @param string[] $externalIds
     * @return Grade[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @param Grade $grade
     */
    public function persist(Grade $grade): void;

    /**
     * @param Grade $grade
     */
    public function remove(Grade $grade): void;
}