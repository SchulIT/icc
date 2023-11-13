<?php

namespace App\Book\IntegrityCheck\Persistence;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Repository\BookIntegrityCheckRunRepositoryInterface;
use App\Repository\BookIntegrityCheckViolationRepositoryInterface;

class ViolationsResolver {
    public function __construct(private readonly BookIntegrityCheckViolationRepositoryInterface $violationRepository,
                                private readonly BookIntegrityCheckRunRepositoryInterface $runRepository) { }

    public function resolve(Student $student, Section $section, ?Teacher $filterTeacher = null): PersistedRun {
        $run = new PersistedRun($student, $this->runRepository->findByStudent($student)?->getLastRun());

        foreach($this->violationRepository->findAllByStudent($student, $section->getStart(), $section->getEnd()) as $violation) {
            if($filterTeacher === null) {
                $run->addViolation($violation);
            } else {
                if ($violation->getLesson() === null) {
                    continue;
                }

                if ($violation->getLesson()->getTeachers()->contains($filterTeacher)) {
                    $run->addViolation($violation);
                }
            }
        }

        return $run;
    }
}