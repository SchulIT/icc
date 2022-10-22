<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupsData {

    /**
     * @Serializer\Type("int")
     */
    private ?int $year = null;

    /**
     * @Serializer\Type("int")
     */
    private ?int $section = null;

    /**
     * @Serializer\Type("array<App\Request\Data\StudyGroupData>")
     * @UniqueId(propertyPath="id")
     * @var StudyGroupData[]
     */
    #[Assert\Valid]
    private array $studyGroups = [ ];

    /**
     * @return StudyGroupData[]
     */
    public function getStudyGroups() {
        return $this->studyGroups;
    }

    /**
     * @param StudyGroupData[] $studyGroups
     */
    public function setStudyGroups($studyGroups): StudyGroupsData {
        $this->studyGroups = $studyGroups;
        return $this;
    }

    public function getYear(): int {
        return $this->year;
    }

    public function setYear(int $year): StudyGroupsData {
        $this->year = $year;
        return $this;
    }

    public function getSection(): int {
        return $this->section;
    }

    public function setSection(int $section): StudyGroupsData {
        $this->section = $section;
        return $this;
    }
}