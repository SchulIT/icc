<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\UserType;

interface AppointmentRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Appointment|null
     */
    public function findOneById(int $id): ?Appointment;

    /**
     * @param string $externalId
     * @return Appointment|null
     */
    public function findOneByExternalId(string $externalId): ?Appointment;

    /**
     * @param Grade $grade
     * @param \DateTime|null $today
     * @param bool $includeHiddenFromStudents
     * @return Appointment[]
     */
    public function findAllForGrade(Grade $grade, ?\DateTime $today = null, bool $includeHiddenFromStudents = false): array;

    /**
     * @param Student[] $students
     * @param \DateTime|null $today
     * @param bool $includeHiddenFromStudents
     * @return Appointment[]
     */
    public function findAllForStudents(array $students, ?\DateTime $today = null, bool $includeHiddenFromStudents = false): array;

    /**
     * @param Teacher $teacher
     * @param \DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForTeacher(Teacher $teacher, ?\DateTime $today = null): array;

    /**
     * @param AppointmentCategory[] $categories
     * @param string|null $q
     * @param \DateTime|null $today = null
     * @return Appointment[]
     */
    public function findAll(array $categories = [ ], ?string $q = null, ?\DateTime $today = null);

    /**
     * @param Appointment $appointment
     */
    public function persist(Appointment $appointment): void;

    /**
     * @param Appointment $appointment
     */
    public function remove(Appointment $appointment): void;
}