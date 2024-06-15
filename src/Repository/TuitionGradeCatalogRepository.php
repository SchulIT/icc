<?php

namespace App\Repository;

use App\Entity\TuitionGradeCatalog;

class TuitionGradeCatalogRepository extends AbstractRepository implements TuitionGradeCatalogRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(TuitionGradeCatalog::class)
            ->findBy([], [
                'displayName' => 'asc'
            ]);
    }

    public function persist(TuitionGradeCatalog $type): void {
        $this->em->persist($type);
        $this->em->flush();
    }

    public function remove(TuitionGradeCatalog $type): void {
        $this->em->remove($type);
        $this->em->flush();
    }
}