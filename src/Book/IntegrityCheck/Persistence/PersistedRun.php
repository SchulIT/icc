<?php

namespace App\Book\IntegrityCheck\Persistence;

use App\Entity\BookIntegrityCheckViolation;
use App\Entity\Student;
use DateTime;

class PersistedRun {

    private array $violations = [ ];

    public function __construct(private readonly Student $student, private readonly ?DateTime $lastRun) { }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getLastRun(): ?DateTime {
        return $this->lastRun;
    }

    public function addViolation(BookIntegrityCheckViolation $violation): void {
        $this->violations[] = $violation;
    }

    /**
     * @return BookIntegrityCheckViolation[]
     */
    public function getViolations(): array {
        return $this->violations;
    }

    public function getNonSuppressedViolations(): array {
        return array_filter($this->violations, fn(BookIntegrityCheckViolation $violation) => $violation->isSuppressed() === false);
    }

    public function getNonSuppressedViolationsCount(): int {
        return count($this->getNonSuppressedViolations());
    }

}