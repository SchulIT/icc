<?php

namespace App\Book\Grade\AdminOverview;

use App\Entity\TuitionGradeCategory;

readonly class Overview {

    /**
     * @param Row[] $rows
     * @param TuitionGradeCategory[] $categories
     */
    public function __construct(public array $rows, public array $categories) { }
}