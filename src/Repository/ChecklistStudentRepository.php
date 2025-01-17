<?php

namespace App\Repository;

use App\Entity\Checklist;
use App\Entity\ChecklistStudent;
use App\Entity\Student;
use App\Entity\User;

class ChecklistStudentRepository extends AbstractRepository implements ChecklistStudentRepositoryInterface {

    public function countCheckedForChecklist(Checklist $checklist): int {
        return $this->em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(ChecklistStudent::class, 'cs')
            ->where('cs.checklist = :checklist')
            ->andWhere('cs.isChecked = true')
            ->setParameter('checklist', $checklist->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNotCheckedForChecklist(Checklist $checklist): int {
        return $this->em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(ChecklistStudent::class, 'cs')
            ->where('cs.checklist = :checklist')
            ->andWhere('cs.isChecked = false')
            ->setParameter('checklist', $checklist->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllByStudent(Student $student, bool $onlyNotChecked = false): array {
        $qb = $this->em->createQueryBuilder()
            ->select(['s', 'c'])
            ->from(ChecklistStudent::class, 's')
            ->join('s.checklist', 'c')
            ->where('s.student = :student')
            ->setParameter('student', $student->getId());

        if($onlyNotChecked === true) {
            $qb->andWhere('s.isChecked = false');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}