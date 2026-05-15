<?php

namespace App\LearningManagementSystem\Repository;

use App\LearningManagementSystem\Entity\LearningManagementSystem;
use App\Framework\Repository\TransactionalRepositoryInterface;

interface LearningManagementSystemRepositoryInterface extends TransactionalRepositoryInterface {

    public function findOneById(int $id): ?LearningManagementSystem;

    public function findAll(): array;

    public function persist(LearningManagementSystem $lms): void;

    public function remove(LearningManagementSystem $lms): void;
}