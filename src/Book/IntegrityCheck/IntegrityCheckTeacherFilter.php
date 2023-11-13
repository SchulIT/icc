<?php

namespace App\Book\IntegrityCheck;

use App\Entity\Teacher;

class IntegrityCheckTeacherFilter {
    public function filter(Teacher $teacher, IntegrityCheckResult $result): IntegrityCheckResult {
        $newResult = new IntegrityCheckResult($result->getStudent(), $result->getStart(), $result->getEnd(), $result->getLastRun());

        foreach($result->getViolations() as $violation) {
            if($violation->getTimetableLesson() === null) {
                continue;
            }

            if($violation->getTimetableLesson()->getTeachers()->contains($teacher)) {
                $newResult->addViolation($violation);
            }
        }

        return $newResult;
    }
}