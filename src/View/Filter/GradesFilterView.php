<?php

namespace App\View\Filter;

use App\Entity\Grade;

class GradesFilterView implements FilterViewInterface {

    /** @var Grade[] */
    private $grades;

    /** @var Grade[] */
    private $currentGrades;

    /**
     * GradeFilterView constructor.
     * @param Grade[] $grades
     * @param Grade[] $currentGrades
     */
    public function __construct(array $grades, array $currentGrades) {
        $this->grades = $grades;
        $this->currentGrades = $currentGrades;
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