<?php

namespace App\Messenger;

use App\Book\IntegrityCheck\CachedIntegrityCheckRunner;
use App\Repository\StudentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RunIntegrityCheckHandler {
    public function __construct(private readonly CachedIntegrityCheckRunner $runner, private readonly StudentRepositoryInterface $studentRepository) { }

    public function __invoke(RunIntegrityCheckMessage $message): void {
        $student = $this->studentRepository->findOneById($message->getStudentId());

        if($student === null) {
            // Student does not exist anymore -> nothing to do here
            return;
        }

        $this->runner->runChecks($student, $message->getStart(), $message->getEnd());
    }
}