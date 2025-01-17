<?php

namespace App\Sorting;

use App\Entity\ChecklistStudent;

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