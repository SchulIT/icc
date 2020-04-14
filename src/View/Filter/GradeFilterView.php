<?php

namespace App\View\Filter;

use App\Entity\Grade;

class GradeFilterView implements FilterViewInterface {

    /** @var Grade[] */
    private $grades;

    /** @var Grade|null */
    private $currentGrade;

    /**
     * GradeFilterView constructor.
     * @param Grade[] $grades
     * @param Grade|null $grade
     */
    public function __construct(array $grades, ?Grade $grade) {
        $this->grades = $grades;
        $this->currentGrade = $grade;
    }

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @return Grade|null
     */
    public function getCurrentGrade(): ?Grade {
        return $this->currentGrade;
    }

    public function isEnabled(): bool {
        return count($this->grades) > 0;
    }
}