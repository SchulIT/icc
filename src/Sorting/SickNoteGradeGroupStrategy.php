<?php

namespace App\Sorting;

use App\Grouping\SickNoteGradeGroup;

class SickNoteGradeGroupStrategy implements SortingStrategyInterface {

    private $gradeStrategy;

    public function __construct(GradeNameStrategy $gradeNameStrategy) {
        $this->gradeStrategy = $gradeNameStrategy;
    }

    /**
     * @param SickNoteGradeGroup $objectA
     * @param SickNoteGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}