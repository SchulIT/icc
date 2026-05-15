<?php

namespace App\LearningManagementSystem\Repository;

use App\Framework\Repository\AbstractTransactionalRepository;
use App\LearningManagementSystem\Entity\LearningManagementSystem;
use App\Common\Entity\Student;
use App\LearningManagementSystem\Entity\StudentLearningManagementSystemInformation;
use App\LearningManagementSystem\Repository\StudentLearningManagementInformationRepositoryInterface;

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

    public function isPasswordSetByStudentAndLms(Student $student, LearningManagementSystem $lms): bool {
        return $this->em->createQueryBuilder()
                ->select('COUNT(1)')
                ->from(StudentLearningManagementSystemInformation::class, 'i')
                ->where('i.student = :student')
                ->andWhere('i.lms = :lms')
                ->andWhere('i.password IS NOT NULL')
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