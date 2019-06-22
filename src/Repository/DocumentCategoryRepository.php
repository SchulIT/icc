<?php

namespace App\Repository;

use App\Entity\DocumentCategory;
use Doctrine\ORM\EntityManagerInterface;

class DocumentCategoryRepository extends AbstractRepository implements DocumentCategoryRepositoryInterface {

    /**
     * @param int $id
     * @return DocumentCategory|null
     */
    public function findOneById(int $id): ?DocumentCategory {
        $this->em->getRepository(DocumentCategory::class)
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

    /**
     * @param DocumentCategory $category
     */
    public function persist(DocumentCategory $category): void {
        $this->em->persist($category);
        $this->em->flush();
    }

    /**
     * @param DocumentCategory $category
     */
    public function remove(DocumentCategory $category): void {
        $this->em->remove($category);
        $this->em->flush();
    }
}