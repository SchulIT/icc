<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Grade;
use App\Framework\View\Filter\FilterViewInterface;

class GradeFilterView implements FilterViewInterface {

    /**
     * GradeFilterView constructor.
     * @param Grade[] $grades
     * @param Grade[] $ownGrades
     */
    public function __construct(private readonly array $grades, private readonly ?Grade $currentGrade, private readonly array $ownGrades)
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

    public function getOwnGrades(): array {
        return $this->ownGrades;
    }

    public function isEnabled(): bool {
        return count($this->grades) > 0;
    }
}