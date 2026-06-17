<?php

namespace App\Book\Grade\AdminOverview;

use App\Book\Entity\TuitionGradeCategory;
use App\Common\Entity\Tuition;

readonly class Row {

    /**
     * @param Tuition $tuition
     * @param TuitionGradeCategory[] $categories
     */
    public function __construct(public Tuition $tuition, public array $categories) { }

    public function isCategoryEnabled(TuitionGradeCategory $category): bool {
        return in_array($category, $this->categories);
    }
}