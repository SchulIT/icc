<?php

namespace App\View\Filter;

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
    private $sorter;
    private $grouper;
    private $studyGroupRepository;

    public function __construct(Sorter $sorter, Grouper $grouper, StudyGroupRepositoryInterface $studyGroupRepository) {
        $this->sorter = $sorter;
        $this->grouper = $grouper;
        $this->studyGroupRepository = $studyGroupRepository;
    }

    public function handle(?string $studyGroupUuid, User $user, bool $onlyGrades = false) {
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        $studyGroups = [ ];

        if($isStudentOrParent) {
            /** @var Student[] $students */
            $students = $user->getStudents();
            $studyGroups = [ ];

            foreach($students as $student) {
                $studyGroups = array_merge(
                    $studyGroups,
                    $student->getStudyGroupMemberships()->map(function(StudyGroupMembership $membership) {
                        return $membership->getStudyGroup();
                    })->toArray()
                );
            }
        } else {
            $studyGroups = $this->studyGroupRepository->findAll();
        }

        if($onlyGrades === true) {
            $studyGroups = array_filter($studyGroups, function(StudyGroup $studyGroup) {
                return $studyGroup->getType()->equals(StudyGroupType::Grade());
            });
        }

        $studyGroups = ArrayUtils::createArrayWithKeys(
            $studyGroups,
            function(StudyGroup $studyGroup) {
                return (string)$studyGroup->getUuid();
            }
        );

        $studyGroup = $studyGroupUuid !== null ?
            $studyGroups[$studyGroupUuid] ?? null : null;

        $groups = $this->grouper->group($studyGroups, StudyGroupTypeStrategy::class);
        $this->sorter->sort($groups, StudyGroupTypeGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, StudyGroupStrategy::class);

        return new StudyGroupFilterView($groups, $studyGroup);
    }
}