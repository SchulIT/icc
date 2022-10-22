<?php

namespace App\Response\Api\V1;

class StudyGroupMembershipList {

    /**
     * @Serializer\SerializedName("memberships")
     * @Serializer\Type("array<App\Response\Api\V1\StudyGroupMembership>")
     *
     * @var StudyGroupMembership[]
     */
    private ?array $memberships = null;

    /**
     * @return StudyGroupMembership[]
     */
    public function getMemberships(): array {
        return $this->memberships;
    }

    /**
     * @param StudyGroupMembership[] $memberships
     */
    public function setMemberships(array $memberships): StudyGroupMembershipList {
        $this->memberships = $memberships;
        return $this;
    }
}