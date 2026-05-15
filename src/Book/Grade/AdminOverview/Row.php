<?php

namespace App\Book\Grade\AdminOverview;

use App\Common\Entity\Grade;
use App\Common\Entity\Tuition;
use App\Grade\Entity\TuitionGradeCategory;

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