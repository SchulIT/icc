<?php

namespace App\Book\Grade;

use App\Entity\TuitionGradeCategory;

class GradeOverview {
    public function __construct(
        /** @var TuitionGradeCategory[] */
        private readonly array $categories,
        /** @var GradeRow[] */
        private readonly array $rows) { }

    /**
     * @return TuitionGradeCategory[]
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