<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\Grouper;
use App\Grouping\StudentGradeStrategy;
use App\Repository\StudentRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;

class StudentFilter {

    private $sorter;
    private $grouper;
    private $studentRepository;

    public function __construct(Sorter $sorter, Grouper $grouper, StudentRepositoryInterface $studentRepository) {
        $this->sorter = $sorter;
        $this->grouper = $grouper;
        $this->studentRepository = $studentRepository;
    }

    public function handle(?string $studentUuid, ?Section $section, User $user, bool $setDefaultStudent = true): StudentFilterView {
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        if($isStudentOrParent) {
            $students = $user->getStudents()->toArray();
        } else if($section !== null) {
            $students = $this->studentRepository->findAllBySection($section);
        } else {
            $students = [ ];
        }

        $students = ArrayUtils::createArrayWithKeys(
            $students,
            function(Student $student) {
                return (string)$student->getUuid();
            }
        );

        $student = $studentUuid !== null ?
            $students[$studentUuid] ?? null : null;

        if($student === null && $user->getStudents()->count() > 0) {
            $student = $user->getStudents()->first();
        }

        if($setDefaultStudent === false) {
            $student = null;
        }

        if($section !== null) {
            $groups = $this->grouper->group($students, StudentGradeStrategy::class, ['section' => $section]);
            $this->sorter->sort($groups, StudentGradeGroupStrategy::class);
            $this->sorter->sortGroupItems($groups, StudentStrategy::class);
        } else {
            $groups = [ ];
        }

        return new StudentFilterView($groups, $student);
    }
}