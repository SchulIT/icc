<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupsData {

    /**
     * @Serializer\Type("array<StudyGroupData>")
     * @Assert\Valid()
     * @var StudyGroupData[]
     */
    private $studyGroups;

    /**
     * @return StudyGroupData[]
     */
    public function getStudyGroups(): array {
        return $this->studyGroups;
    }

    /**
     * @param StudyGroupData[] $studyGroups
     * @return StudyGroupsData
     */
    public function setStudyGroups(array $studyGroups): StudyGroupsData {
        $this->studyGroups = $studyGroups;
        return $this;
    }
}