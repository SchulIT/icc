<?php

namespace App\Book\IntegrityCheck;

use App\Book\IntegrityCheck\Persistence\IntegrityCheckPersister;
use App\Entity\Student;
use App\Settings\BookSettings;
use App\Sorting\IntegrityCheckViolationStrategy;
use App\Sorting\Sorter;
use DateTime;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class IntegrityCheckRunner {
    /**
     * @param IntegrityCheckInterface[] $checks
     */
    public function __construct(#[AutowireIterator('app.book.integrity_check')] private readonly iterable $checks, private readonly BookSettings $bookSettings,
                                private readonly Sorter $sorter, private readonly IntegrityCheckPersister $persister) { }

    public function runChecks(Student $student, DateTime $start, DateTime $end): IntegrityCheckResult {
        $result = new IntegrityCheckResult($student, $start, $end, new DateTime('now'));

        $violations = [ ];
        foreach($this->getEnabledChecks() as $check) {
            $violations = array_merge($violations, $check->check($student, $start, $end));
        }

        // Sort results
        $this->sorter->sort($violations, IntegrityCheckViolationStrategy::class);

        $result->addViolations($violations);

        $this->persister->persist($result);

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