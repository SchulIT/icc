<?php

namespace App\Book\Grade\Export\ZP10;

use App\Entity\TuitionGrade;
use App\Entity\TuitionGradeCategory;
use App\Repository\TuitionGradeRepositoryInterface;
use App\Utils\ArrayUtils;
use Doctrine\Common\Collections\ArrayCollection;

class Exporter {

    public function __construct(private readonly TuitionGradeRepositoryInterface $repository) {

    }

    private function collectTuitions(Configuration $configuration): array {
        $tuitions = [ ];

        if($configuration->section === null) {
            return $tuitions;
        }

        /** @var TuitionGradeCategory $category */
        foreach([$configuration->abschlussNote, $configuration->muendlich, $configuration->schriftlich, $configuration->vornote] as $category) {
            foreach($category->getTuitions() as $tuition) {
                if($tuition->getSection()->getId() === $configuration->section->getId()) {
                    $tuitions[] = $tuition;
                }
            }
        }

        return ArrayUtils::unique($tuitions);
    }

    public function createView(Configuration $config): View {
        $rows = [ ];

        foreach($this->collectTuitions($config) as $tuition) {
            $grades = new ArrayCollection($this->repository->findAllByTuition($tuition));

            foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                $student = $membership->getStudent();

                $gradeAbschluss = $grades->findFirst(fn(int $idx, TuitionGrade $grade) => $student->getId() === $grade->getStudent()->getId() && $grade->getCategory() === $config->abschlussNote);
                $gradeVornote = $grades->findFirst(fn(int $idx, TuitionGrade $grade) => $student->getId() === $grade->getStudent()->getId() && $grade->getCategory() === $config->vornote);
                $gradeSchriftlich = $grades->findFirst(fn(int $idx, TuitionGrade $grade) => $student->getId() === $grade->getStudent()->getId() && $grade->getCategory() === $config->schriftlich);
                $gradeMuendlich = $grades->findFirst(fn(int $idx, TuitionGrade $grade) => $student->getId() === $grade->getStudent()->getId() && $grade->getCategory() === $config->muendlich);

                $rows[] = new Row(
                    $student,
                    $tuition->getSubject(),
                    $gradeAbschluss,
                    $gradeVornote,
                    $gradeSchriftlich,
                    $gradeMuendlich
                );
            }
        }

        return new View($rows);
    }
}