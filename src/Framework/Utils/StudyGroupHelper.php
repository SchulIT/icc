<?php

namespace App\Framework\Utils;

use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupMembership;
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