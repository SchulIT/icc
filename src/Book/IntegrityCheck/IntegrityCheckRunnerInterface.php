<?php

namespace App\Book\IntegrityCheck;

use App\Entity\Student;
use DateTime;

interface IntegrityCheckRunnerInterface {
    public function runChecks(Student $student, DateTime $start, DateTime $end): IntegrityCheckResult;

    /**
     * @return IntegrityCheckRunnerInterface[]
     */
    public function getEnabledChecks(): iterable;
}