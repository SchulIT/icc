<?php

namespace App\Sorting;

use App\Entity\StudentLearningManagementSystemInformation;

readonly class StudentLearningManagementSystemInformationStrategy implements SortingStrategyInterface {

    public function __construct(private LearningManagementSystemNameStrategy $strategy) {

    }

    /**
     * @param StudentLearningManagementSystemInformation $objectA
     * @param StudentLearningManagementSystemInformation $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->strategy->compare($objectA->getLms(), $objectB->getLms());
    }
}
