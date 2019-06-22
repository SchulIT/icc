<?php

namespace App\Repository;

use App\Entity\GradeTeacher;

class GradeTeacherRepository extends AbstractTransactionalRepository implements GradeTeacherRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(GradeTeacher::class)
            ->findAll();
    }

    public function persist(GradeTeacher $gradeTeacher): void {
        $this->em->persist($gradeTeacher);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete()
            ->from(GradeTeacher::class, 'g')
            ->getQuery()
            ->execute();
    }



}