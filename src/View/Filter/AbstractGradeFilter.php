<?php

namespace App\View\Filter;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use FervoEnumBundle\Generated\Form\GradeTeacherTypeType;

abstract class AbstractGradeFilter {

    public function __construct(protected Sorter $sorter, protected GradeRepositoryInterface $gradeRepository, protected TuitionRepositoryInterface $tuitionRepository)
    {
    }

    /**
     * @param User $user
     * @param Section|null $section
     * @param Grade|null $defaultGrade
     * @return Grade[]
     */
    protected function getGrades(User $user, ?Section $section, ?Grade &$defaultGrade): array {
        $isStudentOrParent = $user->isStudentOrParent();
        $defaultGrade = null;

        if($isStudentOrParent) {
            if($section === null) {
                return [ ];
            }

            $grades = $user->getStudents()->map(fn(Student $student) => $student->getGrade($section))->toArray();
            $defaultGrade = $grades[0] ?? null;
        } else {
            $grades = $this->gradeRepository->findAll();
        }

        if($user->getTeacher() !== null) {
            /** @var GradeTeacher $gradeTeacher */
            foreach($user->getTeacher()->getGrades() as $gradeTeacher) {
                if($gradeTeacher->getSection() === $section) {
                    if($defaultGrade === null) {
                        $defaultGrade = $gradeTeacher->getGrade();
                    } else if($gradeTeacher->getType() === GradeTeacherType::Primary) {
                        $defaultGrade = $gradeTeacher->getGrade();
                    }
                }
            }
        }

        return ArrayUtils::createArrayWithKeys(
            array_filter(
                $grades,
                fn($grade) => $grade !== null),
            fn(Grade $grade) => (string)$grade->getUuid()
        );
    }

    /**
     * @param Teacher $teacher
     * @param Section $section
     * @return Grade[]
     */
    protected function getTeachedGradesForTeacher(Teacher $teacher, Section $section): array {
        $tuition = $this->tuitionRepository->findAllByTeacher($teacher, $section);

        $grades = [ ];

        foreach($tuition as $tuition) {
            foreach($tuition->getStudyGroup()->getGrades() as $grade) {
                $grades[] = $grade;
            }
        }

        $grades = array_unique($grades);

        $this->sorter->sort($grades, GradeNameStrategy::class);

        return $grades;
    }
}