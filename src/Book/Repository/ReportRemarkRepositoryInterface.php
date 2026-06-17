<?php

namespace App\Book\Repository;

use App\Book\Entity\ReportRemark;
use App\Common\Entity\Section;
use App\Common\Entity\User;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;

interface ReportRemarkRepositoryInterface {

    /**
     * @return ReportRemark[]
     */
    public function findAll(Section $section): array;

    /**
     * @param PaginationQuery $paginationQuery
     * @param Section $section
     * @param User $createdBy
     * @return PaginatedResult<ReportRemark>
     */
    public function findBySectionPaginated(PaginationQuery $paginationQuery, Section $section, User $createdBy): PaginatedResult;

    public function persist(ReportRemark $reportRemark): void;

    public function remove(ReportRemark $reportRemark): void;
}
