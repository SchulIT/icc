<?php

namespace App\View\Filter;

use App\Entity\Grade;

class GradeFilterView implements FilterViewInterface {

    /**
     * GradeFilterView constructor.
     * @param Grade[] $grades
     */
    public function __construct(private array $grades, private ?Grade $currentGrade)
    {
    }

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    public function getCurrentGrade(): ?Grade {
        return $this->currentGrade;
    }

    public function isEnabled(): bool {
        return count($this->grades) > 0;
    }
}