<?php

namespace App\Repository;

use App\Entity\GradeTeacher;
use Doctrine\ORM\EntityManagerInterface;

class GradeTeacherRepository implements GradeTeacherRepositoryInterface {

    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(GradeTeacher::class)
            ->findAll();
    }

    public function persist(GradeTeacher $gradeTeacher): void {
        $this->em->persist($gradeTeacher);
        $this->isTransactionActive || $this->em->flush();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete()
            ->from(GradeTeacher::class, 'g')
            ->getQuery()
            ->execute();
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isTransactionActive = true;
    }

    public function commit(): void {
        $this->em->flush();
        $this->em->commit();
        $this->isTransactionActive = false;
    }


}