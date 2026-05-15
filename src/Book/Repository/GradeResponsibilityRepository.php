<?php

namespace App\Book\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Common\Entity\Grade;
use App\Book\Entity\GradeResponsibility;
use App\Common\Entity\Section;
use App\Book\Repository\GradeResponsibilityRepositoryInterface;

class GradeResponsibilityRepository extends AbstractRepository implements GradeResponsibilityRepositoryInterface {

    public function findAllByGrade(Grade $grade, Section $section): array {
        return $this->em->getRepository(GradeResponsibility::class)
            ->findBy([
                'grade' => $grade,
                'section' => $section
            ], [
                'task' => 'asc'
            ]);
    }

    public function persist(GradeResponsibility $responsibility): void {
        $this->em->persist($responsibility);
        $this->em->flush();
    }

    public function remove(GradeResponsibility $responsibility): void {
        $this->em->remove($responsibility);
        $this->em->flush();
    }
}