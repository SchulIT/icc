<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;

interface GradeRepositoryInterface extends TransactionalRepositoryInterface {

    public function findOneById(int $id): ?Grade;

    public function findOneByUuid(string $uuid): ?Grade;

    public function findOneByName(string $name): ?Grade;

    public function findOneByExternalId(string $externalId): ?Grade;

    /**
     * @return Grade[]
     */
    public function findAll();

    /**
     * @param string[] $externalIds
     * @return Grade[]
     */
    public function findAllByExternalId(array $externalIds): array;

    public function persist(Grade $grade): void;

    public function remove(Grade $grade): void;
}