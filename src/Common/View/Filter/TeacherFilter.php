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
use App\Common\View\Filter\TeacherFilterView;

class TeacherFilter {

    public function __construct(private Sorter $sorter, private TeacherRepositoryInterface $teacherRepository, private readonly GeneralSettings $generalSettings, private readonly SectionRepositoryInterface $sectionRepository)
    {
    }

    public function handle(?string $uuid, ?Section $section, User $user, bool $setDefaultTeacher, bool $alwaysShowEveryTeacher = false): TeacherFilterView {
        $isStudentOrParent = $user->isStudentOrParent();

        $teachers = [ ];

        if($alwaysShowEveryTeacher || ($isStudentOrParent !== true && $section !== null)) {
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