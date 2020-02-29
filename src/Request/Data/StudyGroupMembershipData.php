<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipData {

    /**
     * Student ID.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $student;

    /**
     * External study group ID which the student belongs to.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $studyGroup;

    /**
     * Type of the membership.
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $type;

    /**
     * @return string|null
     */
    public function getStudent(): ?string {
        return $this->student;
    }

    /**
     * @param string|null $student
     * @return StudyGroupMembershipData
     */
    public function setStudent(?string $student): StudyGroupMembershipData {
        $this->student = $student;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStudyGroup(): ?string {
        return $this->studyGroup;
    }

    /**
     * @param string|null $studyGroup
     * @return StudyGroupMembershipData
     */
    public function setStudyGroup(?string $studyGroup): StudyGroupMembershipData {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return StudyGroupMembershipData
     */
    public function setType(?string $type): StudyGroupMembershipData {
        $this->type = $type;
        return $this;
    }
}