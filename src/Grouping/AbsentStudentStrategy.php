<?php

namespace App\Grouping;

use App\Dashboard\AbsentAppointmentStudent;
use App\Dashboard\AbsentExamStudent;
use App\Dashboard\AbsentStudent;
use App\Entity\Appointment;
use App\Entity\Exam;

class AbsentStudentStrategy implements GroupingStrategyInterface {

    /**
     * @param AbsentStudent $object
     * @return Appointment|Exam|null
     */
    public function computeKey($object, array $options = [ ]) {
        if($object instanceof AbsentExamStudent) {
            return $object->getExam();
        }

        if($object instanceof AbsentAppointmentStudent) {
            return $object->getAppointment();
        }

        return null;
    }

    /**
     * @param Appointment|Exam|null $keyA
     * @param Appointment|Exam|null $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param AbsentStudent $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new AbsentStudentGroup($key);
    }
}