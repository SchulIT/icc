<?php

namespace App\Privacy\Repository;

use App\Privacy\Entity\PrivacyCategory;
use App\Framework\Repository\TransactionalRepositoryInterface;

interface PrivacyCategoryRepositoryInterface extends TransactionalRepositoryInterface {
    /**
     * @return PrivacyCategory[]
     */
    public function findAll(): array;

    public function persist(PrivacyCategory $category): void;

    public function remove(PrivacyCategory $category): void;
}