<?php

namespace App\ParentsDay\Repository;

use App\ParentsDay\Entity\ParentsDay;
use App\ParentsDay\Entity\ParentsDayParentalInformation;
use App\Common\Entity\Student;
use App\Common\Entity\Teacher;
use App\Common\Entity\Tuition;
use App\Framework\Repository\TransactionalRepositoryInterface;

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