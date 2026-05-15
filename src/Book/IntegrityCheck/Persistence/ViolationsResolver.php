<?php

namespace App\Book\IntegrityCheck\Persistence;

use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\Teacher;
use App\Book\Repository\BookIntegrityCheckRunRepositoryInterface;
use App\Book\Repository\BookIntegrityCheckViolationRepositoryInterface;

class ViolationsResolver {
    public function __construct(private readonly BookIntegrityCheckViolationRepositoryInterface $violationRepository,
                                private readonly BookIntegrityCheckRunRepositoryInterface $runRepository) { }

    public function resolve(Student $student, Section $section, ?Teacher $filterTeacher = null): PersistedRun {
        $run = new PersistedRun($student, $this->runRepository->findByStudent($student)?->getLastRun());

        foreach($this->violationRepository->findAllByStudent($student, $section->getStart(), $section->getEnd()) as $violation) {
            if($filterTeacher === null) {
                $run->addViolation($violation);
            } else {
                if ($violation->getLesson() === null && $violation->getEvent() === null) {
                    continue;
                }

                if ($violation->getLesson() !== null && $violation->getLesson()->getTeachers()->contains($filterTeacher)) {
                    $run->addViolation($violation);
                }

                if($violation->getEvent() !== null && $violation->getEvent()->getTeacher()?->getId() === $filterTeacher->getId()) {
                    $run->addViolation($violation);
                }
            }
        }

        return $run;
    }
}