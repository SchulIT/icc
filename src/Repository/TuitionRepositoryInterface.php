<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;

interface TuitionRepositoryInterface {

    /**
     * @param int $id
     * @return Tuition|null
     */
    public function findOneById(int $id): ?Tuition;

    /**
     * @param string $externalId
     * @return Tuition|null
     */
    public function findOneByExternalId(string $externalId): ?Tuition;

    /**
     * @param string[] $externalIds
     * @return Tuition[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @param Teacher $teacher
     * @return Tuition[]
     */
    public function findAllByTeacher(Teacher $teacher);

    /**
     * @param Student[] $students
     * @return Tuition[]
     */
    public function findAllByStudents(array $students);

    /**
     * @return Tuition[]
     */
    public function findAll();

    /**
     * @param Tuition $tuition
     */
    public function persist(Tuition $tuition): void;

    /**
     * @param Tuition $tuition
     */
    public function remove(Tuition $tuition): void;
}