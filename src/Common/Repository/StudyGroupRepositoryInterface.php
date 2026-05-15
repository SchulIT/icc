<?php

namespace App\Common\Repository;

use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupType;
use App\Framework\Repository\TransactionalRepositoryInterface;

interface StudyGroupRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return StudyGroup|null
     */
    public function findOneById(int $id): ?StudyGroup;

    /**
     * @param string $uuid
     * @return StudyGroup|null
     */
    public function findOneByUuid(string $uuid): ?StudyGroup;

    /**
     * @param string $externalId
     * @return StudyGroup|null
     */
    public function findOneByExternalId(string $externalId, Section $section): ?StudyGroup;

    /**
     * @param Grade $grade
     * @return StudyGroup|null
     */
    public function findOneByGrade(Grade $grade, Section $section): ?StudyGroup;

    /**
     * @param string $name
     * @return StudyGroup|null
     */
    public function findOneByGradeName(string $name, Section $section): ?StudyGroup;

    /**
     * @param string[] $externalIds
     * @return StudyGroup[]
     */
    public function findAllByExternalId(array $externalIds, Section $section): array;

    /**
     * @return StudyGroup[]
     */
    public function findAllByGrades(Grade $grade, Section $section, ?StudyGroupType $type = null);

    /**
     * @return StudyGroup[]
     */
    public function findAll();

    /**
     * @return StudyGroup[]
     */
    public function findAllBySection(Section $section);

    /**
     * @param StudyGroup $studyGroup
     */
    public function persist(StudyGroup $studyGroup): void;

    /**
     * @param StudyGroup $studyGroup
     */
    public function remove(StudyGroup $studyGroup): void;
}