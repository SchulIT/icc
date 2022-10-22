<?php

namespace App\Sorting;

use App\Entity\StudyGroup;

class StudyGroupStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy, private GradeNameStrategy $gradeStrategy)
    {
    }

    /**
     * @param StudyGroup $objectA
     * @param StudyGroup $objectB
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