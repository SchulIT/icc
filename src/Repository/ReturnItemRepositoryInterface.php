<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\ReturnItem;
use App\Entity\ReturnItemType;
use App\Entity\Student;
use DateTime;
use DateTimeImmutable;

interface ReturnItemRepositoryInterface {
    /**
     * @return PaginatedResult<ReturnItem>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery, ?ReturnItemType $type = null): PaginatedResult;

    /**
     * @param Student[] $students
     * @param PaginationQuery $paginationQuery
     * @param ReturnItemType|null $type
     * @return PaginatedResult<ReturnItem>
     */
    public function findByStudentsPaginated(array $students, PaginationQuery $paginationQuery, ?ReturnItemType $type = null): PaginatedResult;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return ReturnItem[]
     */
    public function findByStudent(Student $student, DateTime $start, DateTime $end): array;

    public function countByType(ReturnItemType $type): int;

    /**
     * @param Student[] $students
     * @return int
     */
    public function countNonReturnedForStudents(array $students): int;

    public function countForRange(DateTime $start, DateTime $end, ReturnItemType|null $type = null): int;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param ReturnItemType|null $type
     * @return ReturnItem[]
     */
    public function findForRange(DateTime $start, DateTime $end, ReturnItemType|null $type = null): array;

    public function persist(ReturnItem $returnItem): void;

    public function remove(ReturnItem $returnItem): void;
}