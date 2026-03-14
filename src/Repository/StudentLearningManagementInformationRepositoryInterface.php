<?php

namespace App\Repository;

use App\Entity\LearningManagementSystem;
use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;

interface StudentLearningManagementInformationRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findAll(): array;

    /**
     * @param LearningManagementSystem $lms
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findAllByLms(LearningManagementSystem $lms): array;

    public function findOneByStudentAndLms(Student $student, LearningManagementSystem $lms): ?StudentLearningManagementSystemInformation;

    public function isConsentedByStudentAndLms(Student $student, LearningManagementSystem $lms): bool;

    /**
     * @param Student $student
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findByStudent(Student $student): array;

    public function persist(StudentLearningManagementSystemInformation $information): void;

    public function removeAll(): void;
}