<?php

namespace App\TeacherAbsence\Repository;

use App\Framework\Repository\AbstractRepository;
use App\TeacherAbsence\Repository\TeacherAbsenceTypeRepositoryInterface;
use App\TeacherAbsence\Entity\TeacherAbsenceType;

class TeacherAbsenceTypeRepository extends AbstractRepository implements TeacherAbsenceTypeRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(TeacherAbsenceType::class)
            ->findBy([], ['name' => 'asc']);
    }

    public function persist(TeacherAbsenceType $type): void {
        $this->em->persist($type);
        $this->em->flush();
    }

    public function remove(TeacherAbsenceType $type): void {
        $this->em->remove($type);
        $this->em->flush();
    }
}