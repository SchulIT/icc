<?php

namespace App\Book\IntegrityCheck\Persistence;

use App\Book\IntegrityCheck\IntegrityCheckResult;
use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Entity\BookEvent;
use App\Entity\BookIntegrityCheckRun;
use App\Entity\BookIntegrityCheckViolation;
use App\Entity\Student;
use App\Entity\TimetableLesson;
use App\Repository\BookIntegrityCheckRunRepositoryInterface;
use App\Repository\BookIntegrityCheckViolationRepositoryInterface;
use App\Utils\ArrayUtils;

class IntegrityCheckPersister {
    public function __construct(private readonly BookIntegrityCheckViolationRepositoryInterface $repository, private readonly BookIntegrityCheckRunRepositoryInterface $runRepository) { }

    public function persist(IntegrityCheckResult $result): void {
        /** @var BookIntegrityCheckViolation[] $existingViolations */
        $existingViolations = ArrayUtils::createArrayWithKeys(
            $this->repository->findAllByStudent($result->getStudent(), $result->getStart(), $result->getEnd()),
            fn(BookIntegrityCheckViolation $violation) => $violation->getReferenceId()
        );

        $this->repository->beginTransaction();

        $currentReferenceIds = [ ];
        foreach($result->getViolations() as $violation) {
            $referenceId = $this->computeReferenceId($violation, $result->getStudent());
            $currentReferenceIds[] = $referenceId;
            if(!array_key_exists($referenceId, $existingViolations)) {
                // new violation
                $violationEntity = (new BookIntegrityCheckViolation())
                    ->setReferenceId($referenceId)
                    ->setDate($violation->getDate())
                    ->setStudent($result->getStudent())
                    ->setIsSuppressed(false)
                    ->setLessonNumber($violation->getLesson())
                    ->setMessage($violation->getMessage())
                    ->setLesson($violation->getTimetableLesson())
                    ->setEvent($violation->getEvent());
            } else {
                $violationEntity = $existingViolations[$referenceId];
            }

            $violationEntity->setUpdatedAt();
            $this->repository->persist($violationEntity);
        }

        // Find removals
        foreach($existingViolations as $existingViolation) {
            if(!in_array($existingViolation->getReferenceId(), $currentReferenceIds)) {
                $this->repository->remove($existingViolation);
            }
        }

        $this->repository->commit();

        $run = $this->runRepository->findByStudent($result->getStudent());

        if($run === null) {
            $run = (new BookIntegrityCheckRun())
                ->setStudent($result->getStudent());
        }

        $run->setLastRun($result->getLastRun());
        $this->runRepository->persist($run);
    }

    private function getObjectiveId(BookEvent|TimetableLesson $lessonOrEvent): ?int {
        return $lessonOrEvent->getId();
    }

    public function computeReferenceId(IntegrityCheckViolation $violation, Student $student): string {
        $id = sprintf(
            '%d-%s-%d-%d-%s',
            $student->getId(),
            $violation->getDate()->format('Y-m-d'),
            $violation->getLesson(),
            $this->getObjectiveId($violation->getEvent() ?? $violation->getTimetableLesson()),
            hash('sha256', $violation->getMessage())
        );

        return $id;
    }
}