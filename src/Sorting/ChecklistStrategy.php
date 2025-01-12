<?php

namespace App\Sorting;

use App\Entity\Checklist;

readonly class ChecklistStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy, private StringStrategy $stringStrategy) {

    }

    /**
     * @param Checklist $objectA
     * @param Checklist $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getDueDate() === null && $objectB->getDueDate() === null) {
            return $this->stringStrategy->compare($objectA->getTitle(), $objectB->getTitle());
        }

        // invert result to ensure recent due dates are further above
        return (-1) * $this->dateStrategy->compare($objectA->getDueDate(), $objectB->getDueDate());
    }
}