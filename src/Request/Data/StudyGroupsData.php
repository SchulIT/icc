<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupsData {

    /**
     * @Serializer\Type("array<App\Request\Data\StudyGroupData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var StudyGroupData[]
     */
    private $studyGroups = [ ];

    /**
     * @return StudyGroupData[]
     */
    public function getStudyGroups() {
        return $this->studyGroups;
    }

    /**
     * @param StudyGroupData[] $studyGroups
     * @return StudyGroupsData
     */
    public function setStudyGroups($studyGroups): StudyGroupsData {
        $this->studyGroups = $studyGroups;
        return $this;
    }
}