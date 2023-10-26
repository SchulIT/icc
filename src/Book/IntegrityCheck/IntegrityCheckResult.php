<?php

namespace App\Book\IntegrityCheck;

use App\Entity\Student;
use DateTime;

class IntegrityCheckResult {

    /**
     * @var IntegrityCheckViolation[]
     */
    private array $violations = [ ];

    public function __construct(private readonly Student $student, private readonly DateTime $start, private readonly DateTime $end) { }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getStart(): DateTime {
        return $this->start;
    }

    public function getEnd(): DateTime {
        return $this->end;
    }

    public function addViolation(IntegrityCheckViolation $violation): void {
        $this->violations[] = $violation;
    }

    public function addViolations(array $violations): void {
        foreach($violations as $violation) {
            $this->addViolation($violation); // This ensures type safety
        }
    }

    /**
     * @return IntegrityCheckViolation[]
     */
    public function getViolations(): array {
        return $this->violations;
    }
}