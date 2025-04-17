<?php

namespace App\Repository;

/**
 * @template T
 */
readonly class PaginatedResult {

    /**
     * @param T[] $result
     * @param int $totalCount
     */
    public function __construct(public array $result, public int $totalCount) {

    }
}