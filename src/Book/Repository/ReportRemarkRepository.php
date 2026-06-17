<?php

namespace App\Book\Repository;

use App\Book\Entity\ReportRemark;
use App\Common\Entity\Section;
use App\Common\Entity\User;
use App\Framework\Repository\AbstractRepository;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;
use Override;

class ReportRemarkRepository extends AbstractRepository implements ReportRemarkRepositoryInterface {

    public function findAll(Section $section): array {
        $qb = $this->em->createQueryBuilder()
            ->select(['r', 'sec', 'stu'])
            ->from(ReportRemark::class, 'r')
            ->leftJoin('r.section', 'sec')
            ->leftJoin('r.student', 'stu')
            ->where('r.section = :section')
            ->orderBy('stu.lastname', 'ASC')
            ->addorderBy('stu.firstname', 'ASC')
            ->setParameter('section', $section->getId());

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function findBySectionPaginated(PaginationQuery $paginationQuery, Section $section, User $createdBy): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select(['r', 'sec', 'stu'])
            ->from(ReportRemark::class, 'r')
            ->leftJoin('r.section', 'sec')
            ->leftJoin('r.student', 'stu')
            ->where('r.section = :section')
            ->andWhere('r.createdBy = :createdBy')
            ->orderBy('stu.lastname', 'ASC')
            ->addorderBy('stu.firstname', 'ASC')
            ->setParameter('section', $section->getId())
            ->setParameter('createdBy', $createdBy->getId());

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }

    #[Override]
    public function persist(ReportRemark $reportRemark): void {
        $this->em->persist($reportRemark);
        $this->em->flush();
    }

    #[Override]
    public function remove(ReportRemark $reportRemark): void {
        $this->em->remove($reportRemark);
        $this->em->flush();
    }
}
