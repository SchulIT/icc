<?php

namespace App\Repository;

use App\Entity\TuitionGradeType;

class TuitionGradeTypeRepository extends AbstractRepository implements TuitionGradeTypeRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(TuitionGradeType::class)
            ->findBy([], [
                'displayName' => 'asc'
            ]);
    }

    public function persist(TuitionGradeType $type): void {
        $this->em->persist($type);
        $this->em->flush();
    }

    public function remove(TuitionGradeType $type): void {
        $this->em->remove($type);
        $this->em->flush();
    }
}