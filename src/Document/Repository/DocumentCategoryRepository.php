<?php

namespace App\Document\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Document\Entity\DocumentCategory;
use App\Document\Repository\DocumentCategoryRepositoryInterface;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;
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
    public function findAll(): array {
        return $this->em->getRepository(DocumentCategory::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function findPaginated(PaginationQuery $paginationQuery): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select('c')
            ->from(DocumentCategory::class, 'c')
            ->orderBy('c.name', 'ASC');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
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