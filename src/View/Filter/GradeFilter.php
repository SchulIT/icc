<?php

namespace App\View\Filter;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\GradeRepositoryInterface;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class GradeFilter {
    private $sorter;
    private $gradeRepository;

    public function __construct(Sorter $sorter, GradeRepositoryInterface $gradeRepository) {
        $this->sorter = $sorter;
        $this->gradeRepository = $gradeRepository;
    }

    public function handle(?int $gradeId, User $user) {
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());
        $defaultGrade = null;

        if($isStudentOrParent) {
            $grades = $user->getStudents()->map(function(Student $student) {
                return $student->getGrade();
            })->toArray();
            $defaultGrade = $grades[0] ?? null;
        } else {
            $grades = $this->gradeRepository->findAll();
        }

        $grades = ArrayUtils::createArrayWithKeys(
            $grades,
            function(Grade $grade) {
                return $grade->getId();
            }
        );

        $grade = $gradeId !== null ?
            $grades[$gradeId] ?? $defaultGrade : $defaultGrade;

        $this->sorter->sort($grades, GradeNameStrategy::class);

        return new GradeFilterView($grades, $grade);
    }
}