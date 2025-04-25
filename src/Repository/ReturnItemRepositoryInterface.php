<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\ReturnItem;
use App\Entity\ReturnItemType;
use App\Entity\Student;

interface ReturnItemRepositoryInterface {
    /**
     * @return PaginatedResult<ReturnItem>
     */
    public function findAllPaginated(int &$page, int &$limit, ?ReturnItemType $type = null): PaginatedResult;

    /**
     * @param Student[] $students
     * @param int $page
     * @param int $limit
     * @param ReturnItemType|null $type
     * @return PaginatedResult<ReturnItem>
     */
    public function findByStudentsPaginated(array $students, int &$page, int &$limit, ?ReturnItemType $type = null): PaginatedResult;

    public function countByType(ReturnItemType $type): int;

    /**
     * @param Student[] $students
     * @return int
     */
    public function countNonReturnedForStudents(array $students): int;

    public function persist(ReturnItem $returnItem): void;

    public function remove(ReturnItem $returnItem): void;
}