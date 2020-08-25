<?php

namespace App\Sorting;

use App\Entity\StudyGroup;

class StudyGroupStrategy implements SortingStrategyInterface {

    private $stringStrategy;
    private $gradeStrategy;

    public function __construct(StringStrategy $strategy, GradeNameStrategy $gradeStrategy) {
        $this->stringStrategy = $strategy;
        $this->gradeStrategy = $gradeStrategy;
    }

    /**
     * @param StudyGroup $objectA
     * @param StudyGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $nameCmp = $this->stringStrategy->compare($objectA->getName(), $objectB->getName());

        if($nameCmp === 0) {
            // Sort by grades
            foreach($objectA->getGrades() as $gradeA) {
                foreach($objectB->getGrades() as $gradeB) {
                    $gradeCmp = $this->gradeStrategy->compare($gradeA, $gradeB);

                    if($gradeCmp !== 0) {
                        return $gradeCmp;
                    }
                }
            }
        }

        return $nameCmp;
    }
}