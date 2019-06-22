<?php

namespace App\Repository;

use App\Entity\DocumentVisibility;

class DocumentVisibilityRepository extends AbstractRepository implements DocumentVisibilityRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(DocumentVisibility::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(DocumentVisibility $documentVisibility): void {
        $this->em->persist($documentVisibility);
        $this->em->flush();
    }
}