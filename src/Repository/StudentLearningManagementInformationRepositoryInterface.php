<?php

namespace App\Repository;

use App\Entity\StudentLearningManagementSystemInformation;

interface StudentLearningManagementInformationRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findAll(): array;

    public function persist(StudentLearningManagementSystemInformation $information): void;

    public function removeAll(): void;
}