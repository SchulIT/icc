<?php

namespace App\Repository;

use App\Entity\LearningManagementSystem;
use App\Entity\StudentLearningManagementSystemInformation;

interface StudentLearningManagementInformationRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findAll(): array;

    /**
     * @param LearningManagementSystem $lms
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findByLms(LearningManagementSystem $lms): array;

    public function persist(StudentLearningManagementSystemInformation $information): void;

    public function removeAll(): void;
}