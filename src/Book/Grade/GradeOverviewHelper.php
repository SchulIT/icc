<?php

namespace App\Book\Grade;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\TuitionGrade;
use App\Repository\TuitionGradeRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Sorting\TuitionGradeCategoryStrategy;
use App\Sorting\TuitionStrategy;
use App\Utils\ArrayUtils;

class GradeOverviewHelper {

    public function __construct(private readonly TuitionGradeRepositoryInterface $tuitionGradeRepository, private readonly TuitionRepositoryInterface $tuitionRepository, private readonly Sorter $sorter) { }

    public function computeOverviewForStudent(Student $student, Section $section): GradeOverview {
        $tuitions = $this->tuitionRepository->findAllByStudents([$student], $section);
        $categories = [ ];

        foreach($tuitions as $tuition) {
            foreach($tuition->getGradeCategories() as $category) {
                $categories[$category->getId()] = $category;
            }
        }

        $grades = ArrayUtils::createArrayWithKeys(
            $this->tuitionGradeRepository->findAllByStudent($student, $section),
            fn(TuitionGrade $grade) => sprintf('%s_%s', $grade->getStudent()->getId(), $grade->getCategory()->getId())
        );

        $this->sorter->sort($tuitions, TuitionStrategy::class);

        $rows = [ ];
        foreach($tuitions as $tuition) {
            $data = [ ];

            foreach($tuition->getGradeCategories() as $category) {
                $key = sprintf('%s_%s', $student->getId(), $category->getId());
                $data[$category->getUuid()->toString()] = $grades[$key] ?? null;
            }

            $rows[] = new GradeRow($tuition, $data);
        }

        $this->sorter->sort($categories, TuitionGradeCategoryStrategy::class);
        return new GradeOverview($categories, $rows);
    }

    public function computeOverviewForTuition(Tuition $tuition): GradeOverview {
        $categories = $tuition->getGradeCategories()->toArray();

        $grades = ArrayUtils::createArrayWithKeys(
            $this->tuitionGradeRepository->findAllByTuition($tuition),
            fn(TuitionGrade $grade) => sprintf('%s_%s', $grade->getStudent()->getId(), $grade->getCategory()->getId())
        );

        $rows = [ ];
        /** @var Student[] $students */
        $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

        $this->sorter->sort($students, StudentStrategy::class);

        foreach($students as $student) {
            $data = [ ];

            foreach($categories as $category) {
                $key = sprintf('%s_%s', $student->getId(), $category->getId());
                $data[$category->getUuid()->toString()] = $grades[$key] ?? null;
            }

            $rows[] = new GradeRow($student, $data);
        }

        $this->sorter->sort($categories, TuitionGradeCategoryStrategy::class);
        return new GradeOverview($categories, $rows);
    }
}