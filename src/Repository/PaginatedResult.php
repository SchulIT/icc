<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use IteratorAggregate;
use Override;
use Traversable;

/**
 * @template T
 */
class PaginatedResult implements IteratorAggregate {

    public int $totalPages {
        get {
            if($this->limit === 0) {
                return 0;
            }

            return ceil((float)$this->totalCount / $this->limit);
        }
    }

    /**
     * @param Traversable<array-key, T> $iterator
     * @param int $totalCount
     * @param int $page
     * @param int $limit
     */
    public function __construct(
        public Traversable $iterator,
        public int $totalCount,
        public int $page,
        public int $limit) { }

    #[Override]
    public function getIterator(): Traversable {
        return $this->iterator;
    }

    /**
     * @param Query $query
     * @param PaginationQuery $paginationQuery
     * @return PaginatedResult<T>
     * @throws Exception
     */
    public static function fromQuery(Query $query, PaginationQuery $paginationQuery): PaginatedResult {
        $paginator = new Paginator($query, fetchJoinCollection: true);
        return new PaginatedResult(
            $paginator->getIterator(),
            $paginator->count(),
            $paginationQuery->page,
            $paginationQuery->limit
        );
    }

    /**
     * @param QueryBuilder $qb
     * @param PaginationQuery $paginationQuery
     * @return PaginatedResult<T>
     * @throws Exception
     */
    public static function fromQueryBuilder(QueryBuilder $qb, PaginationQuery $paginationQuery): PaginatedResult {
        $qb->setMaxResults($paginationQuery->limit)
            ->setFirstResult($paginationQuery->getOffset());

        return self::fromQuery($qb->getQuery(), $paginationQuery);
    }
}
