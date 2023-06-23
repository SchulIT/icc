<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface AppointmentRepositoryInterface extends TransactionalRepositoryInterface {

    public function findOneById(int $id): ?Appointment;

    public function findOneByExternalId(string $externalId): ?Appointment;

    /**
     * @param int[] $ids
     * @return Appointment[]
     */
    public function findAllByIds(array $ids): array;

    /**
     * @return Appointment[]
     */
    public function findAllForStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null): array;

    /**
     * @param Student[] $students
     * @return Appointment[]
     */
    public function findAllForStudents(array $students, ?DateTime $today = null): array;

    /**
     * Finds all appointments for the given day (or all appointments) which
     * have a visibility set to students.
     */
    public function findAllForAllStudents(?DateTime $today = null): array;

    /**
     * @param Student[] $students
     * @return Appointment[]
     */
    public function findAllForStudentsAndTime(array $students, DateTime $start, DateTime $end): array;

    /**
     * @return Appointment[]
     */
    public function findAllForTeacher(Teacher $teacher, ?DateTime $today = null): array;

    /**
     * @param AppointmentCategory[] $categories
     * @param DateTime|null $today = null
     * @return Appointment[]
     */
    public function findAll(array $categories = [ ], ?string $q = null, ?DateTime $today = null);

    /**
     * @param AppointmentCategory[] $categories
     * @return Appointment[]
     */
    public function findAllStartEnd(DateTime $start, DateTime $end, array $categories = [ ]): array;

    public function countNotConfirmed(): int;

    public function persist(Appointment $appointment): void;

    public function remove(Appointment $appointment): void;

    public function removeBetween(DateTime $start, DateTime $end): int;

    public function getPaginator(int $itemsPerPage, int &$page, array $categories = [ ], ?string $q = null, ?User $createdBy = null, ?bool $confirmed = null): Paginator;
}