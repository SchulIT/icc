<?php

namespace App\View\Filter;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\GradeRepositoryInterface;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use FervoEnumBundle\Generated\Form\GradeTeacherTypeType;

abstract class AbstractGradeFilter {

    public function __construct(protected Sorter $sorter, protected GradeRepositoryInterface $gradeRepository)
    {
    }

    /**
     * @param Grade|null $defaultGrade
     * @return Grade[]
     */
    protected function getGrades(User $user, ?Section $section, &$defaultGrade): array {
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());
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

        $grades = ArrayUtils::createArrayWithKeys(
            array_filter(
                $grades,
                fn($grade) => $grade !== null),
            fn(Grade $grade) => (string)$grade->getUuid()
        );

        return $grades;
    }
}