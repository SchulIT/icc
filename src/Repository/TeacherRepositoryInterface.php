<?php

namespace App\Repository;

use App\Entity\Teacher;

interface TeacherRepositoryInterface {

    /**
     * @param int $id
     * @return Teacher|null
     */
    public function findOneById(int $id): ?Teacher;

    /**
     * @param string $acronym
     * @return Teacher|null
     */
    public function findOneByAcronym(string $acronym): ?Teacher;

    /**
     * @param string[] $acronyms
     * @return Teacher[]
     */
    public function findAllByAcronym(array $acronyms): array;

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