<?php

namespace App\Repository;

use App\Entity\TuitionGradeCategory;

class TuitionGradeCategoryRepository extends AbstractRepository implements TuitionGradeCategoryRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(TuitionGradeCategory::class)
            ->findBy([], ['position' => 'asc']);
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