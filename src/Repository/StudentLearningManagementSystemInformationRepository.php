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

    public function findAllByLms(LearningManagementSystem $lms): array {
        return $this->em->getRepository(StudentLearningManagementSystemInformation::class)
            ->findBy([
                'lms' => $lms
            ]);
    }

    public function findByStudent(Student $student): array {
        return $this->em->getRepository(StudentLearningManagementSystemInformation::class)
            ->findBy(['student' => $student]);
    }

    public function isConsentedByStudentAndLms(Student $student, LearningManagementSystem $lms): bool {
        return $this->em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(StudentLearningManagementSystemInformation::class, 'i')
            ->where('i.student = :student')
            ->andWhere('i.lms = :lms')
            ->andWhere('i.isConsented = true')
            ->setParameter('student', $student)
            ->setParameter('lms', $lms)
            ->getQuery()
            ->getSingleScalarResult() > 0;
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