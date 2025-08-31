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

    public function findOneByStudentAndLms(Student $student, LearningManagementSystem $lms): ?StudentLearningManagementSystemInformation;

    /**
     * @param Student $student
     * @return StudentLearningManagementSystemInformation[]
     */
    public function findByStudent(Student $student): array;

    public function persist(StudentLearningManagementSystemInformation $information): void;

    public function removeAll(): void;
}