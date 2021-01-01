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
     * @param int[] $ids
     * @return Appointment[]
     */
    public function findAllByIds(array $ids): array;

    /**
     * @param StudyGroup $studyGroup
     * @param DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null): array;

    /**
     * @param Student[] $students
     * @param DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForStudents(array $students, ?DateTime $today = null): array;

    /**
     * Finds all appointments for the given day (or all appointments) which
     * have a visibility set to students.
     *
     * @param DateTime|null $today
     * @return array
     */
    public function findAllForAllStudents(?DateTime $today = null): array;

    /**
     * @param Student[] $students
     * @param DateTime $start
     * @param DateTime $end
     * @return Appointment[]
     */
    public function findAllForStudentsAndTime(array $students, DateTime $start, DateTime $end): array;

    /**
     * @param Teacher $teacher
     * @param DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForTeacher(Teacher $teacher, ?DateTime $today = null): array;

    /**
     * @param AppointmentCategory[] $categories
     * @param string|null $q
     * @param DateTime|null $today = null
     * @return Appointment[]
     */
    public function findAll(array $categories = [ ], ?string $q = null, ?DateTime $today = null);

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param AppointmentCategory[] $categories
     * @return Appointment[]
     */
    public function findAllStartEnd(DateTime $start, DateTime $end, array $categories = [ ]): array;

    /**
     * @return int
     */
    public function countNotConfirmed(): int;

    /**
     * @param Appointment $appointment
     */
    public function persist(Appointment $appointment): void;

    /**
     * @param Appointment $appointment
     */
    public function remove(Appointment $appointment): void;

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param array $categories
     * @param string|null $q
     * @param User|null $createdBy
     * @return Paginator
     */
    public function getPaginator(int $itemsPerPage, int &$page, array $categories = [ ], ?string $q = null, ?User $createdBy = null, ?bool $confirmed = null): Paginator;
}