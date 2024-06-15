<?php

namespace App\Book\Grade\Export\XNM;

use App\Book\Student\StudentInfoResolver;
use App\Entity\TuitionGrade;
use App\Repository\TuitionGradeRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Exporter {
    public function __construct(private readonly TuitionGradeRepositoryInterface $repository, private readonly StudentInfoResolver $infoResolver) {}

    public function createView(Configuration $configuration): View {
        $rows = [ ];

        foreach($configuration->tuitions as $tuition) {
            $grades = new ArrayCollection($this->repository->findAllByTuition($tuition));

            foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                $student = $membership->getStudent();
                $studentInfo = $this->infoResolver->resolveStudentInfo($student, $configuration->section, [$tuition]);

                $grade = $grades->findFirst(fn(int $idx, TuitionGrade $grade) => $student->getId() === $grade->getStudent()->getId() && $grade->getCategory()->getId() === $configuration->notenKategorie->getId());
                $rows[] = new Row(
                    $student,
                    $tuition,
                    $grade,
                    $membership->getType(),
                    $studentInfo->getAbsentLessonsCount(),
                    $studentInfo->getNotExcusedOrNotSetLessonsCount(),
                    in_array($student->getGrade($configuration->section)?->getName(), [ 'Q1', 'Q2'])
                );
            }
        }

        return new View($rows, $configuration->section);
    }
}