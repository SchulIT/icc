<?php

namespace App\Book\Grade\AdminOverview;

use App\Grade\Entity\TuitionGradeCategory;

readonly class Overview {

    /**
     * @param Row[] $rows
     * @param TuitionGradeCategory[] $categories
     */
    public function __construct(public array $rows, public array $categories) { }
}