<?php

namespace App\Grade\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Grade\Entity\TuitionGradeCategory;
use App\Grade\Entity\TuitionGradeCatalog;
use App\Grade\Repository\TuitionGradeCategoryRepositoryInterface;

class TuitionGradeCategoryRepository extends AbstractRepository implements TuitionGradeCategoryRepositoryInterface {

    public function findOneByUuid(string $uuid): ?TuitionGradeCategory {
        return $this->em->getRepository(TuitionGradeCategory::class)
            ->findOneBy(['uuid' => $uuid]);
    }

    public function findAll(): array {
        return $this->em->getRepository(TuitionGradeCategory::class)
            ->findBy([], ['position' => 'asc']);
    }

    public function findAllByGradeType(TuitionGradeCatalog $catalog): array {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(TuitionGradeCategory::class, 'c')
            ->leftJoin('c.catalog', 't')
            ->where('t.id = :type')
            ->setParameter('type', $catalog->getId())
            ->getQuery()
            ->getResult();
    }

    public function persist(TuitionGradeCategory $category): void {
        $this->em->persist($category);
        $this->em->flush();
    }

    public function remove(TuitionGradeCategory $category): void {
        $this->em->remove($category);
        $this->em->flush();
    }
}