<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Grade;
use App\Common\Entity\GradeTeacher;
use App\Common\Entity\GradeTeacherType;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\Teacher;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Common\Repository\GradeRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Sorting\GradeNameStrategy;
use App\Framework\Sorting\Sorter;
use App\Framework\Utils\ArrayUtils;

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