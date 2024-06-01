<?php

namespace App\Book\Grade;

class GradeOverview {
    public function __construct(
        /** @var Category[] */
        private readonly array $categories,
        /** @var GradeRow[] */
        private readonly array $rows) { }

    /**
     * @return Category[]
     */
    public function getCategories(): array {
        return $this->categories;
    }

    /**
     * @return GradeRow[]
     */
    public function getRows(): array {
        return $this->rows;
    }
}