<?php

namespace App\Sorting;

use App\Converter\GradesStringConverter;
use App\Entity\Tuition;

readonly class TuitionStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $strategy, private GradesStringConverter $gradesStringConverter) { }

    /**
     * @param Tuition $objectA
     * @param Tuition $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $nameCmp = $this->strategy->compare($objectA->getName(), $objectB->getName());

        if($nameCmp !== 0) {
            return $nameCmp;
        }

        return $this->strategy->compare(
            $this->gradesStringConverter->convert($objectA->getStudyGroup()->getGrades()),
            $this->gradesStringConverter->convert($objectB->getStudyGroup()->getGrades())
        );
    }
}