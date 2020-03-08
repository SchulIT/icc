<?php

namespace App\Repository;

use App\Entity\PrivacyCategory;

interface PrivacyCategoryRepositoryInterface extends TransactionalRepositoryInterface {
    /**
     * @return PrivacyCategory[]
     */
    public function findAll(): array;

    public function persist(PrivacyCategory $category): void;

    public function remove(PrivacyCategory $category): void;
}