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

    public function __construct(private Sorter $sorter, private Grouper $grouper, private StudentRepositoryInterface $studentRepository)
    {
    }

    public function handle(?string $studentUuid, ?Section $section, User $user, bool $setDefaultStudent = true): StudentFilterView {
        $isStudentOrParent = $user->isStudentOrParent();

        if($isStudentOrParent || $user->getUserType() === UserType::Intern) {
            $students = $user->getStudents()->toArray();
        } else if($section !== null) {
            $students = $this->studentRepository->findAllBySection($section);
        } else {
            $students = [ ];
        }

        $students = ArrayUtils::createArrayWithKeys(
            $students,
            fn(Student $student) => (string)$student->getUuid()
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