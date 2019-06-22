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

class TeacherFilter {

    private $sorter;
    private $grouper;
    private $teacherRepository;

    public function __construct(Sorter $sorter, Grouper $grouper, TeacherRepositoryInterface $teacherRepository) {
        $this->sorter = $sorter;
        $this->grouper = $grouper;
        $this->teacherRepository = $teacherRepository;
    }

    public function handle(?string $acronym, User $user): TeacherFilterView {
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        $teachers = [ ];

        if($isStudentOrParent !== true) {
            $teachers = $this->teacherRepository->findAll();
        }

        $teachers = ArrayUtils::createArrayWithKeys(
            $teachers,
            function(Teacher $teacher) {
                return $teacher->getAcronym();
            }
        );
        $teacher = $acronym !== null ?
            $teachers[$acronym] ?? $user->getTeacher() : $user->getTeacher();

        $this->sorter->sort($teachers, TeacherStrategy::class);

        return new TeacherFilterView($teachers, $teacher);
    }
}