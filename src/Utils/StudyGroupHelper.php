<?php

namespace App\Utils;

use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use Doctrine\Common\Collections\ArrayCollection;

class StudyGroupHelper {

    /**
     * @param Student[] $students
     * @return ArrayCollection
     */
    public function getStudyGroups(iterable $students): ArrayCollection {
        $studyGroups = new ArrayCollection();

        foreach($students as $student) {
            $studentStudyGroups = $student
                ->getStudyGroupMemberships()
                ->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudyGroup();
                });

            foreach($studentStudyGroups as $studyGroup) {
                if(!$studyGroups->contains($studyGroup)) {
                    $studyGroups->add($studyGroup);
                }
            }
        }

        return $studyGroups;
    }
}