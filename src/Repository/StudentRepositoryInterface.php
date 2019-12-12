<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Sorting\StudentGroupMembershipStrategy;

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
     * @param Grade $grade
     * @return Student[]
     */
    public function findAllByGrade(Grade $grade): array;

    /**
     * @param string $query
     * @return Student[]
     */
    public function findAllByQuery(string $query): array;

    /**
     * @param StudyGroup[] $studyGroups
     * @return Student[]
     */
    public function findAllByStudyGroups(array $studyGroups): array;

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