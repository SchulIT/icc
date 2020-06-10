<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipsData {

    /**
     * @Serializer\Type("array<App\Request\Data\StudyGroupMembershipData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
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
}