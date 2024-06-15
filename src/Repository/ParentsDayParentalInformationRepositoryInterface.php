<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayParentalInformation;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;

interface ParentsDayParentalInformationRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param ParentsDay $parentsDay
     * @param Student $student
     * @return ParentsDayParentalInformation[]
     */
    public function findForStudent(ParentsDay $parentsDay, Student $student): array;

    /**
     * @param ParentsDay $parentsDay
     * @param Teacher $teacher
     * @param Student[] $students
     * @return ParentsDayParentalInformation[]
     */
    public function findForTeacherAndStudents(ParentsDay $parentsDay, Teacher $teacher, array $students): array;

    public function persist(ParentsDayParentalInformation $information): void;
}