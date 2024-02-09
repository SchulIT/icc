<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayAppointment;
use App\Entity\Teacher;

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