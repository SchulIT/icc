<?php

namespace App\Repository;

use App\Entity\BookIntegrityCheckRun;
use App\Entity\Student;

class BookIntegrityCheckRunRepository extends AbstractRepository implements BookIntegrityCheckRunRepositoryInterface {

    public function findByStudent(Student $student): ?BookIntegrityCheckRun {
        return $this->em->getRepository(BookIntegrityCheckRun::class)
            ->findOneBy(['student' => $student]);
    }

    public function persist(BookIntegrityCheckRun $run): void {
        $this->em->persist($run);
        $this->em->flush();
    }

    public function remove(BookIntegrityCheckRun $run): void {
        $this->em->remove($run);
        $this->em->flush();
    }
}