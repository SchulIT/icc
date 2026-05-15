<?php

namespace App\Privacy\Repository;

use App\Framework\Repository\AbstractTransactionalRepository;
use App\Privacy\Entity\PrivacyCategory;
use App\Privacy\Repository\PrivacyCategoryRepositoryInterface;

class PrivacyCategoryRepository extends AbstractTransactionalRepository implements PrivacyCategoryRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(PrivacyCategory::class)
            ->findAll();
    }

    public function persist(PrivacyCategory $category): void {
        $this->em->persist($category);
        $this->flushIfNotInTransaction();
    }

    public function remove(PrivacyCategory $category): void {
        $this->em->remove($category);
        $this->flushIfNotInTransaction();
    }
}