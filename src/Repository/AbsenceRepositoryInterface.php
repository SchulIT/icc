<?php

namespace App\Repository;

use App\Entity\Absence;

interface AbsenceRepositoryInterface extends TransactionalRepositoryInterface {
    public function findAll(): array;

    /**
     * Returns absent teachers for the given date.
     *
     * @param \DateTime $date
     * @return Absence[]
     */
    public function findAllTeachers(\DateTime $date): array;

    /**
     * Returns absent study groups for the given date.
     *
     * @param \DateTime $date
     * @return Absence[]
     */
    public function findAllStudyGroups(\DateTime $date): array;

    public function persist(Absence $person): void;

    public function removeAll(): void;
}