<?php

namespace App\LearningManagementSystem\Repository;

use App\LearningManagementSystem\Entity\LearningManagementSystem;
use App\Common\Entity\Student;
use App\LearningManagementSystem\Entity\StudentLearningManagementSystemInformation;
use App\Framework\Repository\TransactionalRepositoryInterface;

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

    public function isPasswordSetByStudentAndLms(Student $student, LearningManagementSystem $lms): bool;

    /**
     * @param Student $student
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findByStudent(Student $student): array;

    public function persist(StudentLearningManagementSystemInformation $information): void;

    public function removeAll(): void;
}