<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipData {

    /**
     * Student ID.
     */
    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $student = null;

    /**
     * External study group ID which the student belongs to.
     */
    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $studyGroup = null;

    /**
     * Type of the membership.
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
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