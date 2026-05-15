<?php

namespace App\Book\Grade;

use App\Common\Entity\Grade;
use App\Common\Entity\Student;
use App\Common\Entity\Tuition;
use App\Framework\Utils\ArrayUtils;

class GradeOverview {

    public function __construct(
        private readonly Tuition|Student|Grade $objective,
        /** @var Category[] */
        private readonly array $categories,
        /** @var GradeRow[] */
        private readonly array $rows) { }

    /**
     * @return Grade|Student|Tuition
     */
    public function getObjective(): Grade|Student|Tuition {
        return $this->objective;
    }

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