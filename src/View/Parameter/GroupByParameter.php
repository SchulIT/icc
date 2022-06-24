<?php

namespace App\View\Parameter;

use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\SubstitutionGradeStrategy;
use App\Grouping\SubstitutionTeacherStrategy;
use App\Sorting\SubstitutionGradeGroupStrategy;
use App\Sorting\SubstitutionTeacherGroupStrategy;
use Doctrine\ORM\EntityManagerInterface;

class GroupByParameter {
    public const Grades = 'grades';
    public const Teachers = 'teachers';

    private $groupMap = [
        self::Grades => SubstitutionGradeStrategy::class,
        self::Teachers => SubstitutionTeacherStrategy::class
    ];

    private $sortMap = [
        SubstitutionGradeStrategy::class => SubstitutionGradeGroupStrategy::class,
        SubstitutionTeacherStrategy::class => SubstitutionTeacherGroupStrategy::class
    ];

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function canGroup(User $user): bool {
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        return $isStudentOrParent === false;
    }

    public function getGroupingStrategyClassName(?string $grouping, User $user, string $sectionKey): string {
        if($this->canGroup($user) !== true) {
            return $this->groupMap[self::Grades];
        }

        $key = sprintf('group_by.%s', $sectionKey);

        if($grouping === null || !array_key_exists($grouping, $this->groupMap)) {
            $grouping = $user->getData($key, null) ?? self::Teachers;
        } else {
            $user->setData($key, $grouping);
            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->groupMap[$grouping];
    }

    public function getGroupingStrategyKey(string $groupingClass): string {
        return array_search($groupingClass, $this->groupMap);
    }

    public function getSortingStrategyClassName(string $groupingStrategy): string {
        if(!isset($this->sortMap[$groupingStrategy])) {
            throw new \InvalidArgumentException(sprintf('Groupstrategy "%s" is not a recognized grouping strategy', $groupingStrategy));
        }

        return $this->sortMap[$groupingStrategy];
    }
}