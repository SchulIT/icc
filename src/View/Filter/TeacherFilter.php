<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\Grouper;
use App\Repository\SectionRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Settings\GeneralSettings;
use App\Sorting\Sorter;
use App\Sorting\TeacherStrategy;
use App\Utils\ArrayUtils;

class TeacherFilter {

    public function __construct(private Sorter $sorter, private TeacherRepositoryInterface $teacherRepository, private readonly GeneralSettings $generalSettings, private readonly SectionRepositoryInterface $sectionRepository)
    {
    }

    public function handle(?string $uuid, ?Section $section, User $user, bool $setDefaultTeacher): TeacherFilterView {
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

        $teacher = $uuid !== null ?
            $teachers[$uuid] ?? $fallbackTeacher : $fallbackTeacher;

        $this->sorter->sort($teachers, TeacherStrategy::class);

        return new TeacherFilterView($teachers, $teacher, $uuid === '✗');
    }
}