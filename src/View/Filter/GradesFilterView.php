<?php

namespace App\View\Filter;

use App\Entity\Grade;

class GradesFilterView implements FilterViewInterface {

    /**
     * GradeFilterView constructor.
     * @param Grade[] $grades
     * @param Grade[] $currentGrades
     */
    public function __construct(private array $grades, private array $currentGrades)
    {
    }

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @return Grade[]
     */
    public function getCurrentGrades(): array {
        return $this->currentGrades;
    }

    public function isEnabled(): bool {
        return count($this->grades) > 0;
    }
}