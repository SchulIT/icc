<?php

namespace App\Exam\Grouping;

use App\Exam\Grouping\ExamDateGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;
use DateTime;
use App\Exam\Entity\Exam;

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