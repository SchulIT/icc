<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipsData {

    /**
     * @Serializer\Type("array<App\Request\Data\StudyGroupMembershipData>")
     * @Assert\Valid()
     * @var StudyGroupMembershipData[]
     */
    private $students;

    /**
     * @return StudyGroupMembershipData[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param StudyGroupMembershipData[] $students
     * @return StudyGroupMembershipsData
     */
    public function setStudents(array $students): StudyGroupMembershipsData {
        $this->students = $students;
        return $this;
    }
}