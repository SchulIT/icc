<?php

namespace App\Utils;

use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use Doctrine\Common\Collections\ArrayCollection;

class StudyGroupHelper {

    /**
     * @param Student[] $students
     */
    public function getStudyGroups(iterable $students): ArrayCollection {
        /** @var ArrayCollection<StudyGroup> $studyGroups */
        $studyGroups = new ArrayCollection();

        foreach($students as $student) {
            $studentStudyGroups = $student
                ->getStudyGroupMemberships()
                ->map(fn(StudyGroupMembership $membership) => $membership->getStudyGroup());

            /** @var StudyGroup $studyGroup */
            foreach($studentStudyGroups as $studyGroup) {
                if(!$studyGroups->contains($studyGroup)) {
                    $studyGroups->add($studyGroup);
                }
            }
        }

        return $studyGroups;
    }
}