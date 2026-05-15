<?php

namespace App\Exam\Grouping;

use App\Exam\Entity\ExamStudent;
use App\Common\Entity\Tuition;
use App\Exam\Grouping\ExamStudentTuitionGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

class ExamStudentTuitionStrategy implements GroupingStrategyInterface {

    /**
     * @param ExamStudent $object
     * @param array $options
     * @return Tuition|null
     */
    public function computeKey($object, array $options = []) {
        return $object->getTuition();
    }

    /**
     * @param Tuition|null $keyA
     * @param Tuition|null $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = []): bool {
        return $keyA === $keyB;
    }

    /**
     * @param Tuition|null $key
     * @param array $options
     * @return GroupInterface#
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new ExamStudentTuitionGroup($key);
    }
}