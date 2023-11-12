<?php

namespace App\Book\IntegrityCheck;

use App\Entity\Student;
use App\Repository\BookIntegrityCheckRunRepositoryInterface;
use App\Repository\BookIntegrityCheckViolationRepositoryInterface;
use DateTime;

class CachedIntegrityCheckRunner implements IntegrityCheckRunnerInterface {

    public function __construct(private readonly IntegrityCheckPersister $persister, private readonly IntegrityCheckRunner $runner,
                                private readonly BookIntegrityCheckViolationRepositoryInterface $repository, private readonly BookIntegrityCheckRunRepositoryInterface $runRepository) {

    }

    public function runChecks(Student $student, DateTime $start, DateTime $end): IntegrityCheckResult {
        $result = $this->runner->runChecks($student, $start, $end);
        $this->persister->persist($result);

        return $result;
    }

    public function getResults(Student $student, DateTime $start, DateTime $end): IntegrityCheckResult {
        $result = new IntegrityCheckResult($student, $start, $end, $this->runRepository->findByStudent($student)?->getLastRun());

        foreach($this->repository->findAllByStudent($student, $start, $end) as $violation) {
            $result->addViolation(new IntegrityCheckViolation($violation->getDate(), $violation->getLessonNumber(), $violation->getLesson(), $violation->getMessage()));
        }

        return $result;
    }

    public function getEnabledChecks(): iterable {
        return $this->runner->getEnabledChecks();
    }
}