<?php

namespace App\Repository;

use App\Entity\Appointment;
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
     * @param Student[] $students
     * @param \DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForStudents(array $students, ?\DateTime $today = null): array;

    /**
     * @param Teacher $teacher
     * @param \DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForTeacher(Teacher $teacher, ?\DateTime $today = null): array;

    /**
     * @param \DateTime|null $today = null
     * @return Appointment[]
     */
    public function findAll(?\DateTime $today = null);

    /**
     * @param Appointment $appointment
     */
    public function persist(Appointment $appointment): void;

    /**
     * @param Appointment $appointment
     */
    public function remove(Appointment $appointment): void;
}