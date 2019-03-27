<?php

namespace App\Grouping;

use App\Entity\Exam;

class ExamDateStrategy implements GroupingStrategyInterface {

    /**
     * @param Exam $exam
     * @return \DateTime
     */
    public function computeKey($exam) {
        return $exam->getDate();
    }

    /**
     * @param \DateTime $keyA
     * @param \DateTime $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA == $keyB;
    }

    /**
     * @param \DateTime $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new ExamDateGroup($key);
    }
}