<?php

namespace App\ParentsDay\Repository;

use App\ParentsDay\Entity\ParentsDay;
use App\ParentsDay\Entity\ParentsDayAppointment;
use App\Common\Entity\Teacher;

interface ParentsDayAppointmentRepositoryInterface {

    /**
     * @param Teacher $teacher
     * @param ParentsDay $parentsDay
     * @return ParentsDayAppointment[]
     */
    public function findForTeacher(Teacher $teacher, ParentsDay $parentsDay): array;

    /**
     * @param array $students
     * @param ParentsDay $parentsDay
     * @return ParentsDayAppointment[]
     */
    public function findForStudents(array $students, ParentsDay $parentsDay): array;

    public function persist(ParentsDayAppointment $appointment): void;

    public function remove(ParentsDayAppointment $appointment): void;
}