<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Entity\Teacher;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Grouping\Grouper;
use App\Common\Repository\SectionRepositoryInterface;
use App\Common\Repository\TeacherRepositoryInterface;
use App\Common\Settings\GeneralSettings;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\TeacherStrategy;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\TeachersFilterView;

class TeachersFilter {

    public function __construct(private Sorter $sorter, private TeacherRepositoryInterface $teacherRepository, private readonly GeneralSettings $generalSettings, private readonly SectionRepositoryInterface $sectionRepository)
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

            if(count($teachers) === 0 && $this->generalSettings->getCurrentSectionId() !== null && $section->getId() !== $this->generalSettings->getCurrentSectionId()) {
                $section = $this->sectionRepository->findOneById($this->generalSettings->getCurrentSectionId());

                if($section !== null) {
                    $teachers = $this->teacherRepository->findAllBySection($section);
                }
            }
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