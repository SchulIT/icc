<?php

namespace App\Repository;

use App\Entity\LearningManagementSystem;

interface LearningManagementSystemRepositoryInterface extends TransactionalRepositoryInterface {

    public function findAll(): array;

    public function persist(LearningManagementSystem $lms): void;

    public function remove(LearningManagementSystem $lms): void;
}