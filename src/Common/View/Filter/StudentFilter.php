<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Grouping\Grouper;
use App\Common\Grouping\StudentGradeStrategy;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Repository\UserRepositoryInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudentGradeGroupStrategy;
use App\Common\Sorting\StudentStrategy;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\StudentFilterView;
use Ramsey\Uuid\Uuid;

class StudentFilter {

    public function __construct(private readonly Sorter $sorter, private readonly Grouper $grouper, private readonly StudentRepositoryInterface $studentRepository, private readonly UserRepositoryInterface $userRepository)
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

        if($user->getStudents()->count() > 0 && !empty($studentUuid) && Uuid::isValid($studentUuid)) {
            $user->setData('filter.student.last', $studentUuid);
            $this->userRepository->persist($user);
        }

        if($student === null && $user->getStudents()->count() > 0) {
            $student = $user->getStudents()->first();

            if(($lastStudent = $user->getData('filter.student.last')) !== null && is_string($lastStudent) && Uuid::isValid($lastStudent) && isset($students[$lastStudent])) {
                $student = $students[$lastStudent];
            }
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

        return new StudentFilterView($groups, $student, count($students));
    }
}