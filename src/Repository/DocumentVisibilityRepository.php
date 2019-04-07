<?php

namespace App\Repository;

use App\Entity\DocumentVisibility;
use Doctrine\ORM\EntityManagerInterface;

class DocumentVisibilityRepository implements DocumentVisibilityRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

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