<?php

namespace App\Repository;

use App\Entity\LearningManagementSystem;
use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;

class StudentLearningManagementSystemInformationRepository extends AbstractTransactionalRepository implements StudentLearningManagementInformationRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(StudentLearningManagementSystemInformation::class)
            ->findAll();
    }

    public function findOneByStudentAndLms(Student $student, LearningManagementSystem $lms): ?StudentLearningManagementSystemInformation {
        return $this->em->getRepository(StudentLearningManagementSystemInformation::class)
            ->findOneBy([
                'student' => $student,
                'lms' => $lms
            ]);
    }

    public function findByStudent(Student $student): array {
        return $this->em->getRepository(StudentLearningManagementSystemInformation::class)
            ->findBy(['student' => $student]);
    }

    public function persist(StudentLearningManagementSystemInformation $information): void {
        $this->em->persist($information);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete(StudentLearningManagementSystemInformation::class)
            ->getQuery()
            ->execute();
    }
}