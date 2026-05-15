<?php

namespace App\Checklist\Sorting;

use App\Checklist\Entity\ChecklistStudent;
use App\Checklist\Sorting\ChecklistStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

readonly class ChecklistStudentChecklistStrategy implements SortingStrategyInterface {

    public function __construct(private ChecklistStrategy $checklistStrategy) {

    }

    /**
     * @param ChecklistStudent $objectA
     * @param ChecklistStudent $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->checklistStrategy->compare($objectA->getChecklist(), $objectB->getChecklist());
    }
}