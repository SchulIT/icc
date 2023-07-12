<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\StudyGroupType;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\Grouper;
use App\Grouping\StudyGroupGradeStrategy;
use App\Grouping\StudyGroupTypeStrategy;
use App\Repository\StudyGroupRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudyGroupGradeGroupStrategy;
use App\Sorting\StudyGroupStrategy;
use App\Sorting\StudyGroupTypeGroupStrategy;
use App\Utils\ArrayUtils;

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