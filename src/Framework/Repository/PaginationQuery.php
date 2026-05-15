<?php

namespace App\Framework\Repository;

class PaginationQuery {

    public const int DefaultItemsPerPage = 25;
    public const int MaxItemsPerPage = 100;

    public function __construct(public int $page, public int $limit = self::DefaultItemsPerPage) {
        if($this->page < 1) {
            $this->page = 1;
        }

        if($this->limit <= 0) {
            $this->limit = self::DefaultItemsPerPage;
        }

        if($this->limit > self::MaxItemsPerPage) {
            $this->limit = self::MaxItemsPerPage;
        }
    }

    public function getOffset(): int {
        return ($this->page - 1) * $this->limit;
    }
}