<?php

namespace App\View\Filter;

use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\Grouper;
use App\Repository\TeacherRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TeacherStrategy;
use App\Utils\ArrayUtils;

class TeachersFilter {

    private $sorter;
    private $grouper;
    private $teacherRepository;

    public function __construct(Sorter $sorter, Grouper $grouper, TeacherRepositoryInterface $teacherRepository) {
        $this->sorter = $sorter;
        $this->grouper = $grouper;
        $this->teacherRepository = $teacherRepository;
    }

    public function handle(?array $teacherUuids, User $user, bool $setDefaultTeacher): TeachersFilterView {
        if($teacherUuids === null) {
            $teacherUuids = [ ];
        }

        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());
        $teachers = [ ];

        if($isStudentOrParent !== true) {
            $teachers = $this->teacherRepository->findAll();
        }

        $teachers = ArrayUtils::createArrayWithKeys(
            $teachers,
            function(Teacher $teacher) {
                return (string)$teacher->getUuid();
            }
        );

        $fallbackTeacher = $setDefaultTeacher ? $user->getTeacher() : null;

        $currentTeachers = [ ];

        /** @var Teacher $teacher */
        foreach($teachers as $teacher) {
            if(in_array((string)$teacher->getUuid(), $teacherUuids)) {
                $currentTeachers[] = $teacher;
            }
        }

        if(count($currentTeachers) === 0 && $setDefaultTeacher === true && $fallbackTeacher !== null) {
            $currentTeachers[] = $fallbackTeacher;
        }

        $this->sorter->sort($teachers, TeacherStrategy::class);

        return new TeachersFilterView($teachers, $currentTeachers);
    }
}