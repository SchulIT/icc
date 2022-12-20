<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\Grouper;
use App\Repository\TeacherRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TeacherStrategy;
use App\Utils\ArrayUtils;

class TeachersFilter {

    public function __construct(private Sorter $sorter, private TeacherRepositoryInterface $teacherRepository)
    {
    }

    public function handle(?array $teacherUuids, ?Section $section, User $user, bool $setDefaultTeacher): TeachersFilterView {
        if($teacherUuids === null) {
            $teacherUuids = [ ];
        }

        $isStudentOrParent = $user->isStudentOrParent();
        $teachers = [ ];

        if($isStudentOrParent !== true && $section !== null) {
            $teachers = $this->teacherRepository->findAllBySection($section);
        }

        $teachers = ArrayUtils::createArrayWithKeys(
            $teachers,
            fn(Teacher $teacher) => (string)$teacher->getUuid()
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