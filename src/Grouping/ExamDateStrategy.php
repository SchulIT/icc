<?php

namespace App\Grouping;

use DateTime;
use App\Entity\Exam;

class ExamDateStrategy implements GroupingStrategyInterface {

    /**
     * @param Exam $exam
     * @return DateTime
     */
    public function computeKey($exam, array $options = [ ]) {
        return $exam->getDate();
    }

    /**
     * @param DateTime $keyA
     * @param DateTime $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA == $keyB;
    }

    /**
     * @param DateTime $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new ExamDateGroup($key);
    }
}