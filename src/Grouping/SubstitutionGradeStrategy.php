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
    public function computeKey($object, array $options = [ ]) {
        /** @var StudyGroup[] $groups */
        $groups = array_merge($object->getStudyGroups()->toArray(), $object->getReplacementStudyGroups()->toArray());
        $grades = [ ];

        foreach($groups as $group) {
            foreach($group->getGrades() as $grade) {
                $grades[$grade->getId()] = $grade;
            }
        }

        /** @var Grade $grade */
        foreach($object->getReplacementGrades() as $grade) {
            $grades[$grade->getId()] = $grade;
        }

        if(count($grades) === 0) {
            return null;
        }

        return array_values($grades);
    }

    /**
     * @param Grade|null $keyA
     * @param Grade|null $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        if($keyA === null && $keyB === null) {
            return true;
        } else if($keyA === null || $keyB === null) {
            return false;
        }

        return $keyA === $keyB || $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade|null $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new SubstitutionGradeGroup($key);
    }
}