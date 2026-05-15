<?php

namespace App\Book\Repository;

use App\Book\Entity\BookIntegrityCheckRun;
use App\Framework\Repository\AbstractRepository;
use App\Common\Entity\Student;
use App\Book\Repository\BookIntegrityCheckRunRepositoryInterface;

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