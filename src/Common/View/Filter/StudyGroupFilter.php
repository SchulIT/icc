<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\StudyGroupType;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Grouping\Grouper;
use App\Common\Grouping\StudyGroupGradeStrategy;
use App\Common\Grouping\StudyGroupTypeStrategy;
use App\Common\Repository\StudyGroupRepositoryInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudyGroupGradeGroupStrategy;
use App\Common\Sorting\StudyGroupStrategy;
use App\Common\Sorting\StudyGroupTypeGroupStrategy;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\StudyGroupFilterView;

class StudyGroupFilter {
    public function __construct(private Sorter $sorter, private Grouper $grouper, private StudyGroupRepositoryInterface $studyGroupRepository)
    {
    }

    public function handle(?string $studyGroupUuid, ?Section $section, User $user, bool $onlyGrades = false) {
        $isStudentOrParent = $user->isStudentOrParent();

        $studyGroups = [ ];

        if($isStudentOrParent) {
            /** @var Student[] $students */
            $students = $user->getStudents();
            $studyGroups = [ ];

            foreach($students as $student) {
                $studyGroups = array_merge(
                    $studyGroups,
                    $student->getStudyGroupMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudyGroup())->toArray()
                );
            }

            $studyGroups = array_filter($studyGroups, function(StudyGroup $studyGroup) use ($section) {
                if($section === null) {
                    return false;
                }

                return $studyGroup->getSection() === $section;
            });
        } else if($section !== null) {
            $studyGroups = $this->studyGroupRepository->findAllBySection($section);
        }

        if($onlyGrades === true) {
            $studyGroups = array_filter($studyGroups, fn(StudyGroup $studyGroup) => $studyGroup->getType() === StudyGroupType::Grade);
        }

        $studyGroups = ArrayUtils::createArrayWithKeys(
            $studyGroups,
            fn(StudyGroup $studyGroup) => (string)$studyGroup->getUuid()
        );

        $studyGroup = $studyGroupUuid !== null ?
            $studyGroups[$studyGroupUuid] ?? null : null;

        $groups = $this->grouper->group($studyGroups, StudyGroupTypeStrategy::class);
        $this->sorter->sort($groups, StudyGroupTypeGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, StudyGroupStrategy::class);

        return new StudyGroupFilterView($groups, $studyGroup);
    }
}