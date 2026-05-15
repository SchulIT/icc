<?php

namespace App\Framework\Import\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Framework\Import\Entity\ImportDateTime;
use App\Framework\Import\Repository\ImportDateTypeRepositoryInterface;

class ImportDateTypeRepository extends AbstractRepository implements ImportDateTypeRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(ImportDateTime::class)
            ->findAll();
    }

    public function findOneByEntityClass(string $className): ?ImportDateTime {
        return $this->em->getRepository(ImportDateTime::class)
            ->findOneBy([
                'entityClass' => $className
            ]);
    }

    public function persist(ImportDateTime $dateTime): void {
        $this->em->persist($dateTime);
        $this->em->flush();
    }
}