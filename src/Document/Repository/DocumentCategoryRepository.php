<?php

namespace App\Document\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Document\Entity\DocumentCategory;
use App\Document\Repository\DocumentCategoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DocumentCategoryRepository extends AbstractRepository implements DocumentCategoryRepositoryInterface {

    public function findOneById(int $id): ?DocumentCategory {
        return $this->em->getRepository(DocumentCategory::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @return DocumentCategory[]
     */
    public function findAll() {
        return $this->em->getRepository(DocumentCategory::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(DocumentCategory $category): void {
        $this->em->persist($category);
        $this->em->flush();
    }

    public function remove(DocumentCategory $category): void {
        $this->em->remove($category);
        $this->em->flush();
    }
}