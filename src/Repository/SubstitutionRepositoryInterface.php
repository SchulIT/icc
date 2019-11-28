<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Substitution;
use App\Entity\Teacher;

interface SubstitutionRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Substitution|null
     */
    public function findOneById(int $id): ?Substitution;

    /**
     * @param string $externalId
     * @return Substitution|null
     */
    public function findOneByExternalId(string $externalId): ?Substitution;

    /**
     * @return Substitution[]
     */
    public function findAll();

    /**
     * @param \DateTime $date
     * @return Substitution[]
     */
    public function findAllByDate(\DateTime $date);

    /**
     * @param array $studyGroups
     * @param \DateTime|null $date
     * @return Substitution[]
     */
    public function findAllForStudyGroups(array $studyGroups, ?\DateTime $date = null);

    /**
     * @param Teacher $teacher
     * @param \DateTime|null $date
     * @return Substitution[]
     */
    public function findAllForTeacher(Teacher $teacher, ?\DateTime $date = null);

    /**
     * @param Grade $grade
     * @param \DateTime|null $date
     * @return Substitution[]
     */
    public function findAllForGrade(Grade $grade, ?\DateTime $date = null);

    /**
     * @param Substitution $substitution
     */
    public function persist(Substitution $substitution): void;

    /**
     * @param Substitution $substitution
     */
    public function remove(Substitution $substitution): void;
}