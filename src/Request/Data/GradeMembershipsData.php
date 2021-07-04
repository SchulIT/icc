<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeMembershipsData {

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $section;

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $year;

    /**
     * @Serializer\Type("array<App\Request\Data\GradeMembershipData>")
     * @Assert\Valid()
     * @var GradeMembershipData[]
     */
    private $memberships = [ ];

    /**
     * @return int
     */
    public function getSection(): int {
        return $this->section;
    }

    /**
     * @param int $section
     * @return GradeMembershipsData
     */
    public function setSection(int $section): GradeMembershipsData {
        $this->section = $section;
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
     * @return GradeMembershipsData
     */
    public function setYear(int $year): GradeMembershipsData {
        $this->year = $year;
        return $this;
    }

    /**
     * @return GradeMembershipData[]
     */
    public function getMemberships(): array {
        return $this->memberships;
    }

    /**
     * @param GradeMembershipData[] $memberships
     * @return GradeMembershipsData
     */
    public function setMemberships(array $memberships): GradeMembershipsData {
        $this->memberships = $memberships;
        return $this;
    }
}