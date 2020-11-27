<?php

namespace App\Sorting;

use App\Entity\SickNote;

class SickNoteStrategy implements SortingStrategyInterface {

    private $studentStrategy;
    private $dateStrategy;

    public function __construct(StudentStrategy $strategy, DateStrategy $dateStrategy) {
        $this->studentStrategy = $strategy;
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param SickNote $objectA
     * @param SickNote $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $cmpStudents = $this->studentStrategy->compare($objectA->getStudent(), $objectB->getStudent());

        if($cmpStudents !== 0) {
            return $cmpStudents;
        }

        if($objectA->getUntil()->getDate() == $objectB->getUntil()->getDate()) {
            return $objectB->getUntil()->getLesson() - $objectA->getUntil()->getLesson();
        }

        return $this->dateStrategy->compare($objectA->getUntil()->getDate(), $objectB->getUntil()->getDate());
    }
}