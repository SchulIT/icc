<?php

namespace App\Book\IntegrityCheck;

use App\Entity\Student;
use DateTime;

interface IntegrityCheckInterface {

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return IntegrityCheckViolation[]
     */
    public function check(Student $student, DateTime $start, DateTime $end): array;

    /**
     * Returns the internal name for the check, so we can enable and disable the check
     *
     * @return string
     */
    public function getName(): string;
}