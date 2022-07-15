<?php

namespace App\Repository;

use App\Entity\StudentAbsenceType;

class StudentAbsenceTypeRepository extends AbstractRepository implements StudentAbsenceTypeRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(StudentAbsenceType::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(StudentAbsenceType $absenceType): void {
        $this->em->persist($absenceType);
        $this->em->flush();
    }

    public function remove(StudentAbsenceType $absenceType): void {
        $this->em->remove($absenceType);
        $this->em->flush();
    }
}