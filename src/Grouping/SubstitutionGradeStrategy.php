<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\Substitution;

class SubstitutionGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param Substitution $object
     * @return Grade[]|null
     */
    public function computeKey($object) {
        /** @var StudyGroup[] $groups */
        $groups = array_merge($object->getStudyGroups()->toArray(), $object->getReplacementStudyGroups()->toArray());
        $grades = [ ];

        foreach($groups as $group) {
            foreach($group->getGrades() as $grade) {
                $grades[$grade->getId()] = $grade;
            }
        }

        if(count($grades) === 0) {
            return null;
        }

        return array_values($grades);
    }

    /**
     * @param Grade|null $keyA
     * @param Grade|null $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        if($keyA === null && $keyB === null) {
            return true;
        }

        return $keyA === $keyB || $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade|null $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new SubstitutionGradeGroup($key);
    }
}