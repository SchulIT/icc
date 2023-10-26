<?php

namespace App\Book\IntegrityCheck;

use App\Entity\Student;
use App\Settings\BookSettings;
use App\Sorting\IntegrityCheckViolationStrategy;
use App\Sorting\Sorter;
use DateTime;

class IntegrityCheckRunner {

    /**
     * @param IntegrityCheckInterface[] $checks
     */
    public function __construct(private readonly iterable $checks, private readonly BookSettings $bookSettings, private readonly Sorter $sorter) { }

    public function runChecks(Student $student, DateTime $start, DateTime $end): IntegrityCheckResult {
        $result = new IntegrityCheckResult($student, $start, $end);

        $violations = [ ];
        foreach($this->getEnabledChecks() as $check) {
            $violations = array_merge($violations, $check->check($student, $start, $end));
        }

        // Sort results
        $this->sorter->sort($violations, IntegrityCheckViolationStrategy::class);

        $result->addViolations($violations);

        return $result;
    }

    /**
     * @return IntegrityCheckInterface[]
     */
    public function getEnabledChecks(): iterable {
        foreach($this->checks as $check) {
            if($this->bookSettings->isIntegrityCheckEnabled($check->getName())) {
                yield $check;
            }
        }
    }
}