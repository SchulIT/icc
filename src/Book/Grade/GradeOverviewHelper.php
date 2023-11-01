<?php

namespace App\Book\Grade;

use App\Book\StudentsResolver;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\TuitionGrade;
use App\Entity\TuitionGradeCategory;
use App\Repository\TuitionGradeRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Sorting\TuitionGradeCategoryStrategy;
use App\Sorting\TuitionStrategy;
use App\Utils\ArrayUtils;

class GradeOverviewHelper {

    public function __construct(private readonly TuitionGradeRepositoryInterface $tuitionGradeRepository, private readonly TuitionRepositoryInterface $tuitionRepository, private readonly StudentsResolver $studentsResolver, private readonly Sorter $sorter) { }

    public function computeOverviewForStudent(Student $student, Section $section): GradeOverview {
        $tuitions = $this->tuitionRepository->findAllByStudents([$student], $section);
        $categories = [ ];

        foreach($tuitions as $tuition) {
            $gradeCategories = $tuition->getGradeCategories()->toArray();
            $this->sorter->sort($gradeCategories, TuitionGradeCategoryStrategy::class);

            foreach($gradeCategories as $category) {
                $categories[$category->getId()] = new Category($tuition, $category);
            }
        }

        $grades = ArrayUtils::createArrayWithKeys(
            $this->tuitionGradeRepository->findAllByStudent($student, $section),
            fn(TuitionGrade $grade) => sprintf('%s_%s', $grade->getTuition()->getId(), $grade->getCategory()->getId())
        );

        $this->sorter->sort($tuitions, TuitionStrategy::class);

        $rows = [ ];
        foreach($tuitions as $tuition) {
            if($tuition->getGradeCategories()->count() === 0) {
                continue;
            }

            $data = [ ];

            foreach($tuition->getGradeCategories() as $category) {
                $key = sprintf('%s_%s', $tuition->getId(), $category->getId());
                $data[sprintf('%s_%s', $tuition->getUuid()->toString(), $category->getUuid()->toString())] = $grades[$key] ?? null;
            }

            $rows[] = new GradeRow($tuition, $data);
        }

        return new GradeOverview($categories, $rows);
    }

    public function computeOverviewForTuition(Tuition $tuition): ?GradeOverview {
        $categories = $tuition->getGradeCategories()->toArray();

        if(count($categories) === 0) {
            return null;
        }

        $grades = ArrayUtils::createArrayWithKeys(
            $this->tuitionGradeRepository->findAllByTuition($tuition),
            fn(TuitionGrade $grade) => sprintf('%s_%s', $grade->getStudent()->getId(), $grade->getCategory()->getId())
        );

        $rows = [ ];
        /** @var Student[] $students */
        $students = $this->studentsResolver->resolve($tuition, true, true);
        $this->sorter->sort($students, StudentStrategy::class);

        foreach($students as $student) {
            $data = [ ];

            foreach($categories as $category) {
                $key = sprintf('%s_%s', $student->getId(), $category->getId());
                $data[sprintf('%s_%s', $tuition->getUuid()->toString(), $category->getUuid()->toString())] = $grades[$key] ?? null;
            }

            $rows[] = new GradeRow($student, $data);
        }

        $this->sorter->sort($categories, TuitionGradeCategoryStrategy::class);
        $categories = array_map(fn(TuitionGradeCategory $category) => new Category($tuition, $category), $categories);

        return new GradeOverview($categories, $rows);
    }

    public function computeForGrade(Grade $grade, Section $section): GradeOverview {
        $tuitions = $this->tuitionRepository->findAllByGrades([$grade], $section);
        $students = $grade->getMemberships()
            ->filter(fn(GradeMembership $membership) => $membership->getSection()->getId() === $section->getId())
            ->map(fn(GradeMembership $membership) => $membership->getStudent())
            ->toArray();
        $categories = [ ];

        foreach($tuitions as $tuition) {
            $gradeCategories = $tuition->getGradeCategories()->toArray();
            $this->sorter->sort($gradeCategories, TuitionGradeCategoryStrategy::class);

            foreach($gradeCategories as $category) {
                $categories[sprintf('%d-%d', $tuition->getId(), $category->getId())] = new Category($tuition, $category);
            }
        }

        $this->sorter->sort($tuitions, TuitionStrategy::class);
        $this->sorter->sort($students, StudentStrategy::class);

        $rows = [ ];
        foreach($students as $student) {
            $grades = ArrayUtils::createArrayWithKeys(
                $this->tuitionGradeRepository->findAllByStudent($student, $section),
                fn(TuitionGrade $grade) => sprintf('%s_%s', $grade->getTuition()->getId(), $grade->getCategory()->getId())
            );

            $data = [ ];

            foreach($tuitions as $tuition) {
                if($tuition->getGradeCategories()->count() === 0) {
                    continue;
                }

                foreach($tuition->getGradeCategories() as $category) {
                    $key = sprintf('%s_%s', $tuition->getId(), $category->getId());
                    $data[sprintf('%s_%s', $tuition->getUuid()->toString(), $category->getUuid()->toString())] = $grades[$key] ?? null;
                }
            }

            $rows[] = new GradeRow($student, $data);
        }

        return new GradeOverview($categories, $rows);
    }
}