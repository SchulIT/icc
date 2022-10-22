<?php

namespace App\Sorting;

use App\Entity\StudentAbsence;

class StudentAbsenceStrategy implements SortingStrategyInterface {

    public function __construct(private StudentStrategy $studentStrategy, private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param StudentAbsence $objectA
     * @param StudentAbsence $objectB
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