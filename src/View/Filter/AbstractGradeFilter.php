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

    protected $sorter;
    protected $gradeRepository;

    public function __construct(Sorter $sorter, GradeRepositoryInterface $gradeRepository) {
        $this->sorter = $sorter;
        $this->gradeRepository = $gradeRepository;
    }

    /**
     * @param User $user
     * @param Section|null $section
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

            $grades = $user->getStudents()->map(function(Student $student) use($section) {
                return $student->getGrade($section);
            })->toArray();
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
                    } else if($gradeTeacher->getType()->equals(GradeTeacherType::Primary())) {
                        $defaultGrade = $gradeTeacher->getGrade();
                    }
                }
            }
        }

        $grades = ArrayUtils::createArrayWithKeys(
            array_filter(
                $grades,
                function($grade) {
                    return $grade !== null;
                }),
            function(Grade $grade) {
                return (string)$grade->getUuid();
            }
        );

        return $grades;
    }
}