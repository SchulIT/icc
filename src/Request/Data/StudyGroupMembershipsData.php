<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipsData {

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
     * @Serializer\Type("array<App\Request\Data\StudyGroupMembershipData>")
     * @Assert\Valid()
     * @var StudyGroupMembershipData[]
     */
    private $memberships = [ ];

    /**
     * @return StudyGroupMembershipData[]
     */
    public function getMemberships() {
        return $this->memberships;
    }

    /**
     * @param StudyGroupMembershipData[] $memberships
     * @return StudyGroupMembershipsData
     */
    public function setMemberships($memberships): StudyGroupMembershipsData {
        $this->memberships = $memberships;
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
     * @return StudyGroupMembershipsData
     */
    public function setYear(int $year): StudyGroupMembershipsData {
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
     * @return StudyGroupMembershipsData
     */
    public function setSection(int $section): StudyGroupMembershipsData {
        $this->section = $section;
        return $this;
    }
}