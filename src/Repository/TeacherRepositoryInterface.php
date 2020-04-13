<?php

namespace App\Repository;

use App\Entity\Subject;
use App\Entity\Teacher;

interface TeacherRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Teacher|null
     */
    public function findOneById(int $id): ?Teacher;

    /**
     * @param string $uuid
     * @return Teacher|null
     */
    public function findOneByUuid(string $uuid): ?Teacher;

    /**
     * @param string $acronym
     * @return Teacher|null
     */
    public function findOneByAcronym(string $acronym): ?Teacher;

    /**
     * @param string $externalId
     * @return Teacher|null
     */
    public function findOneByExternalId(string $externalId): ?Teacher;

    /**
     * @param string[] $acronyms
     * @return Teacher[]
     */
    public function findAllByAcronym(array $acronyms): array;

    /**
     * @param string[] $externalIds
     * @return Teacher[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @param Subject $subject
     * @return Teacher[]
     */
    public function findAllBySubject(Subject $subject): array;

    /**
     * @return Teacher[]
     */
    public function findAll();

    /**
     * @param Teacher $teacher
     */
    public function persist(Teacher $teacher): void;

    /**
     * @param Teacher $teacher
     */
    public function remove(Teacher $teacher): void;
}