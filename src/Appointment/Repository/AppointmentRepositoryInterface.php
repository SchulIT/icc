<?php

namespace App\Appointment\Repository;

use App\Appointment\Entity\Appointment;
use App\Appointment\Entity\AppointmentCategory;
use App\Common\Entity\Grade;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\Teacher;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;
use App\Framework\Repository\TransactionalRepositoryInterface;
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

    /**
     * @return PaginatedResult<Appointment>
     */
    public function findPaginated(PaginationQuery $paginationQuery, array $categories = [ ], string|null $query = null, User|null $createdBy = null, bool|null $onlyConfirmed = null): PaginatedResult;

    public function countNotConfirmed(): int;

    public function persist(Appointment $appointment): void;

    public function remove(Appointment $appointment): void;

    public function removeBetween(DateTime $start, DateTime $end): int;
}
