<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;

interface StudyGroupRepositoryInterface {

    /**
     * @param int $id
     * @return StudyGroup|null
     */
    public function findOneById(int $id): ?StudyGroup;

    /**
     * @param string $externalId
     * @return StudyGroup|null
     */
    public function findOneByExternalId(string $externalId): ?StudyGroup;

    /**
     * @param string[] $externalIds
     * @return StudyGroup[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @param Grade $grade
     * @param StudyGroupType|null $type
     * @return StudyGroup[]
     */
    public function findAllByGrades(Grade $grade, ?StudyGroupType $type = null);

    /**
     * @param Student $student
     * @return StudyGroup[]
     */
    public function findAllByStudent(Student $student);

    /**
     * @return StudyGroup[]
     */
    public function findAll();

    /**
     * @param StudyGroup $studyGroup
     */
    public function persist(StudyGroup $studyGroup): void;

    /**
     * @param StudyGroup $studyGroup
     */
    public function remove(StudyGroup $studyGroup): void;
}