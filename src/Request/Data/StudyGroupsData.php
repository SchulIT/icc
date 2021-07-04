<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupsData {

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $year;

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $section;

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

    /**
     * @return int
     */
    public function getYear(): int {
        return $this->year;
    }

    /**
     * @param int $year
     * @return StudyGroupsData
     */
    public function setYear(int $year): StudyGroupsData {
        $this->year = $year;
        return $this;
    }

    /**
     * @return int
     */
    public function getSection(): int {
        return $this->section;
    }

    /**
     * @param int $section
     * @return StudyGroupsData
     */
    public function setSection(int $section): StudyGroupsData {
        $this->section = $section;
        return $this;
    }
}