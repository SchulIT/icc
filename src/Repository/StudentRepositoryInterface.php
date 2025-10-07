<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Tuition;
use App\Sorting\StudentGroupMembershipStrategy;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface StudentRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Student|null
     */
    public function findOneById(int $id): ?Student;

    /**
     * @param string $uuid
     * @return Student|null
     */
    public function findOneByUuid(string $uuid): ?Student;

    /**
     * @param string $externalId
     * @return Student|null
     */
    public function findOneByExternalId(string $externalId): ?Student;

    /**
     * @param string $email
     * @return Student|null
     */
    public function findOneByEmailAddress(string $email): ?Student;

    /**
     * @param string $firstname
     * @param string $lastname
     * @param DateTime $dateTime
     * @return Student[]
     */
    public function findAllByNameAndBirthday(string $firstname, string $lastname, DateTime $dateTime): array;

    /**
     * @param int[] $ids
     * @return Student[]
     */
    public function findAllByIds(array $ids): array;

    /**
     * @param string[] $externalIds
     * @return Student[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @param string[] $emailAddresses
     * @return Student[]
     */
    public function findAllByEmailAddresses(array $emailAddresses): array;

    /**
     * @param Grade $grade
     * @param Section $section
     * @return Student[]
     */
    public function findAllByGrade(Grade $grade, Section $section): array;

    /**
     * Finds all students with the given birthday (year is ignored).
     *
     * @param DateTime $date
     * @return Student[]
     */
    public function findAllByBirthday(DateTime $date): array;

    /**
     * @param Grade $grade
     * @param Section $section
     * @return Paginator
     */
    public function getStudentsByGradePaginator(int $itemsPerPage, int &$page, Grade $grade, Section $section): Paginator;

    /**
     * @param string $query
     * @return Student[]
     */
    public function findAllByQuery(string $query): array;

    /**
     * @param StudyGroup[] $studyGroups
     * @return Student[]
     */
    public function findAllByStudyGroups(array $studyGroups): array;

    public function findAllByTuition(Tuition $tuition, array $excludedStatuses = [ ], bool $includeStudentsWithAttendance = false);

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param StudyGroup[] $studyGroups
     * @return Paginator
     */
    public function getStudentsByStudyGroupsPaginator(int $itemsPerPage, int &$page, array $studyGroups): Paginator;

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param Tuition $tuition
     * @param array $excludedStatuses
     * @param bool $includeStudentsWithAttendance
     * @return Paginator
     */
    public function getStudentsByTuitionPaginator(int $itemsPerPage, int &$page, Tuition $tuition, array $excludedStatuses = [ ], bool $includeStudentsWithAttendance = false): Paginator;

    /**
     * @param StudyGroup[] $studyGroups
     * @return QueryBuilder
     */
    public function getQueryBuilderFindAllByStudyGroups(array $studyGroups): QueryBuilder;

    /**
     * @return Student[]
     */
    public function findAll();

    /**
     * @param Section $section
     * @return Student[]
     */
    public function findAllBySection(Section $section): array;

    /**
     * @param Student $student
     */
    public function persist(Student $student): void;

    /**
     * @param Student $student
     */
    public function remove(Student $student): void;

    /**
     * Removes all students without any grade membership.
     * @return int Number of removed students
     */
    public function removeOrphaned(): int;
}