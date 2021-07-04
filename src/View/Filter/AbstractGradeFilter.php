<?php

namespace App\View\Filter;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\GradeRepositoryInterface;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

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

        $grades = ArrayUtils::createArrayWithKeys(
            $grades,
            function(Grade $grade) {
                return (string)$grade->getUuid();
            }
        );

        return $grades;
    }
}