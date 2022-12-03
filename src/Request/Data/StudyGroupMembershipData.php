<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipData {

    /**
     * Student ID.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $student = null;

    /**
     * External study group ID which the student belongs to.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $studyGroup = null;

    /**
     * Type of the membership.
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $type = null;

    public function getStudent(): ?string {
        return $this->student;
    }

    public function setStudent(?string $student): StudyGroupMembershipData {
        $this->student = $student;
        return $this;
    }

    public function getStudyGroup(): ?string {
        return $this->studyGroup;
    }

    public function setStudyGroup(?string $studyGroup): StudyGroupMembershipData {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): StudyGroupMembershipData {
        $this->type = $type;
        return $this;
    }
}