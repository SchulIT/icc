<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipsData {

    /**
     * @Serializer\Type("int")
     */
    private ?int $year = null;

    /**
     * @Serializer\Type("int")
     */
    private ?int $section = null;

    /**
     * @Serializer\Type("array<App\Request\Data\StudyGroupMembershipData>")
     * @var StudyGroupMembershipData[]
     */
    #[Assert\Valid]
    private array $memberships = [ ];

    /**
     * @return StudyGroupMembershipData[]
     */
    public function getMemberships() {
        return $this->memberships;
    }

    /**
     * @param StudyGroupMembershipData[] $memberships
     */
    public function setMemberships($memberships): StudyGroupMembershipsData {
        $this->memberships = $memberships;
        return $this;
    }

    public function getYear(): int {
        return $this->year;
    }

    public function setYear(int $year): StudyGroupMembershipsData {
        $this->year = $year;
        return $this;
    }

    public function getSection(): int {
        return $this->section;
    }

    public function setSection(int $section): StudyGroupMembershipsData {
        $this->section = $section;
        return $this;
    }
}