<?php

namespace App\Repository;

use App\Entity\ExcuseNote;
use App\Entity\Student;

class ExcuseNoteRepository extends AbstractRepository implements ExcuseNoteRepositoryInterface {

    public function findByStudent(Student $student): array {
        return $this->em->getRepository(ExcuseNote::class)
            ->findBy([
                'student' => $student
            ]);
    }

    public function persist(ExcuseNote $note): void {
        $this->em->persist($note);
        $this->em->flush();
    }

    public function remove(ExcuseNote $note): void {
        $this->em->remove($note);
        $this->em->flush();
    }
}