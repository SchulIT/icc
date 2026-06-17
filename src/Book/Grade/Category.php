<?php

namespace App\Book\Grade;

use App\Book\Entity\TuitionGradeCategory;
use App\Common\Entity\Tuition;

class Category {
    public function __construct(private readonly ?Tuition $tuition, private readonly TuitionGradeCategory $category) { }

    /**
     * @return TuitionGradeCategory
     */
    public function getCategory(): TuitionGradeCategory {
        return $this->category;
    }

    /**
     * @return Tuition|null
     */
    public function getTuition(): ?Tuition {
        return $this->tuition;
    }
}