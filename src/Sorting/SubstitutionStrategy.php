<?php

namespace App\Sorting;

use App\Entity\Substitution;

class SubstitutionStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param Substitution $objectA
     * @param Substitution $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $lessonStartCmp = $objectA->getLessonStart() - $objectB->getLessonStart();

        if($lessonStartCmp !== 0) {
            return $lessonStartCmp;
        }

        $startsBeforeCmp = (int)$objectB->startsBefore() - (int)$objectA->startsBefore();

        if($startsBeforeCmp !== 0) {
            return $startsBeforeCmp;
        }

        return $this->stringStrategy->compare($objectA->getSubject(), $objectB->getSubject());
    }
}