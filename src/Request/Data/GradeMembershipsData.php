<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeMembershipsData {

    /**
     * @Serializer\Type("int")
     */
    private ?int $section = null;

    /**
     * @Serializer\Type("int")
     */
    private ?int $year = null;

    /**
     * @Serializer\Type("array<App\Request\Data\GradeMembershipData>")
     * @var GradeMembershipData[]
     */
    #[Assert\Valid]
    private array $memberships = [ ];

    public function getSection(): int {
        return $this->section;
    }

    public function setSection(int $section): GradeMembershipsData {
        $this->section = $section;
        return $this;
    }

    public function getYear(): int {
        return $this->year;
    }

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
     */
    public function setMemberships(array $memberships): GradeMembershipsData {
        $this->memberships = $memberships;
        return $this;
    }
}