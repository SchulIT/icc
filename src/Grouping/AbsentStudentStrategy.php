<?php

namespace App\Grouping;

use App\Dashboard\AbsentBookEventStudent;
use App\Dashboard\AbsentExamStudent;
use App\Dashboard\AbsentStudent;
use App\Entity\Appointment;
use App\Entity\BookEvent;
use App\Entity\Exam;

class AbsentStudentStrategy implements GroupingStrategyInterface {

    /**
     * @param AbsentStudent $object
     * @param array $options
     * @return Exam|BookEvent|null
     */
    public function computeKey($object, array $options = [ ]): Exam|BookEvent|null {
        if($object instanceof AbsentExamStudent) {
            return $object->getExam();
        }

        if($object instanceof AbsentBookEventStudent) {
            return $object->getBookEvent();
        }

        return null;
    }

    /**
     * @param Appointment|Exam|null $keyA
     * @param Appointment|Exam|null $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param Appointment|Exam|null $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new AbsentStudentGroup($key);
    }
}