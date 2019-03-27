<?php

namespace App\Repository;

use App\Entity\Student;

interface StudentRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Student|null
     */
    public function findOneById(int $id): ?Student;

    /**
     * @param string $externalId
     * @return Student|null
     */
    public function findOneByExternalId(string $externalId): ?Student;

    /**
     * @param string[] $externalIds
     * @return Student[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @return Student[]
     */
    public function findAll();

    /**
     * @param Student $student
     */
    public function persist(Student $student): void;

    /**
     * @param Student $student
     */
    public function remove(Student $student): void;
}